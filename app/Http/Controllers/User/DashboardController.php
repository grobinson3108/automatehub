<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TutorialService;
use App\Services\BadgeService;
use App\Services\AnalyticsService;
use App\Services\RestrictionService;
use App\Models\Tutorial;
use App\Models\Download;
use App\Models\UserTutorialProgress;
use App\Models\Favorite;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected TutorialService $tutorialService;
    protected BadgeService $badgeService;
    protected AnalyticsService $analyticsService;
    protected RestrictionService $restrictionService;

    public function __construct(
        TutorialService $tutorialService,
        BadgeService $badgeService,
        AnalyticsService $analyticsService,
        RestrictionService $restrictionService
    ) {
        $this->tutorialService = $tutorialService;
        $this->badgeService = $badgeService;
        $this->analyticsService = $analyticsService;
        $this->restrictionService = $restrictionService;
    }

    /**
     * Display the user dashboard with personalized content.
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Pas de redirection, on affiche le dashboard avec modal si nécessaire

            // Mettre à jour la dernière activité
            $user->update(['last_activity_at' => now()]);

            // Statistiques personnelles avec gestion des erreurs
            $userStats = [
                'total_downloads' => $this->safeCount($user->downloads()),
                'downloads_this_month' => $this->safeCount(
                    $user->downloads()->whereMonth('downloaded_at', Carbon::now()->month)
                ),
                'tutorials_completed' => $this->safeCount(
                    $user->tutorialProgress()->where('completed', true)
                ),
                'tutorials_in_progress' => $this->safeCount(
                    $user->tutorialProgress()->where('completed', false)
                ),
                'favorites_count' => $this->safeCount($user->favorites()),
                'badges_count' => $this->safeCount($user->badges()),
                'current_level' => $user->level_n8n ?? 'beginner',
                'subscription_type' => $user->subscription_type ?? 'free',
                'days_since_registration' => $user->created_at ? $user->created_at->diffInDays(now()) : 0,
            ];

            // Activités récentes avec gestion des erreurs
            $recentActivity = [
                'recent_downloads' => $this->getRecentDownloads($user),
                'recent_progress' => $this->getRecentProgress($user),
                'recent_favorites' => $this->getRecentFavorites($user),
            ];

            // Recommandations personnalisées
            $recommendations = $this->getRecommendations($user);

            // Progression niveau n8n
            $levelProgress = $this->calculateLevelProgress($user);

            // Badges récents et prochains
            $badgeInfo = [
                'recent_badges' => $this->getRecentBadges($user),
                'next_badges' => $this->getNextBadges($user),
            ];

            // Restrictions et limites
            $restrictions = [
                'download_limit' => $this->restrictionService->getDownloadLimit($user),
                'remaining_downloads' => $this->restrictionService->getRemainingDownloads($user->id),
                'can_access_premium' => $this->restrictionService->canAccessPremium($user),
                'can_access_pro' => $this->restrictionService->canAccessPro($user),
            ];

            // Tutoriels suggérés pour continuer
            $continueTutorials = $this->getContinueTutorials($user);

            // Tracking analytics
            $this->analyticsService->track($user->id, 'dashboard_viewed', [
                'subscription_type' => $user->subscription_type,
                'n8n_level' => $user->level_n8n,
            ]);

            // Vérifier si l'onboarding doit être affiché
            $showOnboarding = !$user->onboarding_completed || !$user->level_n8n;
            $onboardingStep = !$user->level_n8n ? 'level' : 'preferences';
            
            return view('user.dashboard.index', compact(
                'userStats',
                'recentActivity',
                'recommendations',
                'levelProgress',
                'badgeInfo',
                'restrictions',
                'continueTutorials',
                'showOnboarding',
                'onboardingStep'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans User/DashboardController::index', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut en cas d'erreur
            $userStats = [
                'total_downloads' => 0,
                'downloads_this_month' => 0,
                'tutorials_completed' => 0,
                'tutorials_in_progress' => 0,
                'favorites_count' => 0,
                'badges_count' => 0,
                'current_level' => 'beginner',
                'subscription_type' => 'free',
                'days_since_registration' => 0,
            ];

            $recentActivity = [
                'recent_downloads' => collect(),
                'recent_progress' => collect(),
                'recent_favorites' => collect(),
            ];

            $recommendations = collect();
            $levelProgress = ['completed_tutorials' => 0, 'total_tutorials' => 0, 'percentage' => 0];
            $badgeInfo = ['recent_badges' => collect(), 'next_badges' => collect()];
            $restrictions = [
                'download_limit' => 10,
                'remaining_downloads' => 10,
                'can_access_premium' => false,
                'can_access_pro' => false,
            ];
            $continueTutorials = collect();

            // Vérifier si l'onboarding doit être affiché
            $showOnboarding = !$user->onboarding_completed || !$user->level_n8n;
            $onboardingStep = !$user->level_n8n ? 'level' : 'preferences';
            
            return view('user.dashboard.index', compact(
                'userStats',
                'recentActivity',
                'recommendations',
                'levelProgress',
                'badgeInfo',
                'restrictions',
                'continueTutorials',
                'showOnboarding',
                'onboardingStep'
            ));
        }
    }

    /**
     * Get user progress data for level advancement.
     */
    public function getProgress(): JsonResponse
    {
        try {
            $user = auth()->user();
            $progress = $this->calculateLevelProgress($user);

            // Calculer les points nécessaires pour le niveau suivant
            $levelPoints = [
                'beginner' => 0,
                'intermediate' => 100,
                'advanced' => 300,
                'expert' => 600,
            ];

            $currentLevel = $user->level_n8n ?? 'beginner';
            $currentPoints = $this->calculateUserPoints($user);
            $currentLevelPoints = $levelPoints[$currentLevel];
            $nextLevel = $this->getNextLevel($currentLevel);
            $nextLevelPoints = $nextLevel ? $levelPoints[$nextLevel] : null;

            $progressData = [
                'current_level' => $currentLevel,
                'current_points' => $currentPoints,
                'next_level' => $nextLevel,
                'points_to_next_level' => $nextLevelPoints ? ($nextLevelPoints - $currentPoints) : 0,
                'progress_percentage' => $progress['percentage'],
                'completed_tutorials' => $progress['completed_tutorials'],
                'total_tutorials_for_level' => $progress['total_tutorials'],
                'recent_achievements' => $this->getRecentAchievements($user),
            ];

            return response()->json($progressData);
        } catch (\Exception $e) {
            \Log::error('Erreur dans User/DashboardController::getProgress', ['error' => $e->getMessage()]);
            return response()->json([
                'current_level' => 'beginner',
                'current_points' => 0,
                'next_level' => 'intermediate',
                'points_to_next_level' => 100,
                'progress_percentage' => 0,
                'completed_tutorials' => 0,
                'total_tutorials_for_level' => 0,
                'recent_achievements' => [],
            ]);
        }
    }

    /**
     * Get user statistics for dashboard widgets.
     */
    public function getStats(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Statistiques de la semaine
            $weeklyStats = [
                'downloads' => $this->safeCount(
                    $user->downloads()->where('downloaded_at', '>=', Carbon::now()->subWeek())
                ),
                'completed_tutorials' => $this->safeCount(
                    $user->tutorialProgress()
                        ->where('completed', true)
                        ->where('updated_at', '>=', Carbon::now()->subWeek())
                ),
                'time_spent' => $this->calculateTimeSpent($user, 7),
            ];

            // Statistiques du mois
            $monthlyStats = [
                'downloads' => $this->safeCount(
                    $user->downloads()->where('downloaded_at', '>=', Carbon::now()->subMonth())
                ),
                'completed_tutorials' => $this->safeCount(
                    $user->tutorialProgress()
                        ->where('completed', true)
                        ->where('updated_at', '>=', Carbon::now()->subMonth())
                ),
                'time_spent' => $this->calculateTimeSpent($user, 30),
            ];

            // Évolution des téléchargements (7 derniers jours)
            $downloadTrend = $user->downloads()
                ->selectRaw('DATE(downloaded_at) as date, COUNT(*) as count')
                ->where('downloaded_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'weekly' => $weeklyStats,
                'monthly' => $monthlyStats,
                'download_trend' => $downloadTrend,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans User/DashboardController::getStats', ['error' => $e->getMessage()]);
            return response()->json([
                'weekly' => ['downloads' => 0, 'completed_tutorials' => 0, 'time_spent' => 0],
                'monthly' => ['downloads' => 0, 'completed_tutorials' => 0, 'time_spent' => 0],
                'download_trend' => [],
            ]);
        }
    }

    /**
     * Get recent activity for the user.
     */
    public function getRecentActivity(): JsonResponse
    {
        try {
            $user = auth()->user();
            $activities = collect();

            // Téléchargements récents
            $downloads = $this->getRecentDownloads($user)->map(function ($download) {
                return [
                    'type' => 'download',
                    'title' => 'Téléchargement de ' . ($download->tutorial->title ?? 'Tutoriel'),
                    'description' => 'Vous avez téléchargé ce tutoriel',
                    'date' => $download->downloaded_at,
                    'tutorial' => $download->tutorial,
                ];
            });

            // Tutoriels complétés récents
            $completions = $this->getRecentProgress($user)
                ->filter(function ($progress) {
                    return $progress->completed;
                })
                ->map(function ($progress) {
                    return [
                        'type' => 'completion',
                        'title' => 'Tutoriel terminé : ' . ($progress->tutorial->title ?? 'Tutoriel'),
                        'description' => 'Félicitations ! Vous avez terminé ce tutoriel',
                        'date' => $progress->updated_at,
                        'tutorial' => $progress->tutorial,
                    ];
                });

            // Badges récents
            $badges = $this->getRecentBadges($user)->map(function ($badge) {
                return [
                    'type' => 'badge',
                    'title' => 'Nouveau badge : ' . $badge->name,
                    'description' => $badge->description,
                    'date' => $badge->pivot->created_at,
                    'badge' => $badge,
                ];
            });

            // Fusionner et trier par date
            $activities = $activities
                ->merge($downloads)
                ->merge($completions)
                ->merge($badges)
                ->sortByDesc('date')
                ->take(20)
                ->values();

            return response()->json($activities);
        } catch (\Exception $e) {
            \Log::error('Erreur dans User/DashboardController::getRecentActivity', ['error' => $e->getMessage()]);
            return response()->json([]);
        }
    }

    /**
     * Safe count method to handle potential errors.
     */
    private function safeCount($query): int
    {
        try {
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get recent downloads safely.
     */
    private function getRecentDownloads($user)
    {
        try {
            return $user->downloads()
                ->with('tutorial:id,title,slug')
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get recent progress safely.
     */
    private function getRecentProgress($user)
    {
        try {
            return $user->tutorialProgress()
                ->with('tutorial:id,title,slug')
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get recent favorites safely.
     */
    private function getRecentFavorites($user)
    {
        try {
            return $user->favorites()
                ->with('tutorial:id,title,slug')
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get recommendations safely.
     */
    private function getRecommendations($user)
    {
        try {
            return $this->tutorialService->getRecommendationsForUser($user->id);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get recent badges safely.
     */
    private function getRecentBadges($user)
    {
        try {
            return $user->badges()
                ->wherePivot('created_at', '>=', Carbon::now()->subDays(30))
                ->orderByPivot('created_at', 'desc')
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get next badges safely.
     */
    private function getNextBadges($user)
    {
        try {
            return $this->badgeService->getAvailableBadges($user->id)->take(3);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get continue tutorials safely.
     */
    private function getContinueTutorials($user)
    {
        try {
            return $user->tutorialProgress()
                ->where('completed', false)
                ->with('tutorial')
                ->latest()
                ->limit(3)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Calculate user's level progress.
     */
    private function calculateLevelProgress($user): array
    {
        try {
            $completedTutorials = $this->safeCount(
                $user->tutorialProgress()->where('completed', true)
            );

            // Tutoriels recommandés pour le niveau actuel - Correction : utiliser required_level au lieu de difficulty_level
            $levelTutorials = Tutorial::where('required_level', $user->level_n8n ?? 'beginner')
                ->where('is_draft', false)
                ->whereNotNull('published_at')
                ->count();

            $percentage = $levelTutorials > 0 ? 
                min(100, ($completedTutorials / $levelTutorials) * 100) : 0;

            return [
                'completed_tutorials' => $completedTutorials,
                'total_tutorials' => $levelTutorials,
                'percentage' => round($percentage, 1),
            ];
        } catch (\Exception $e) {
            return [
                'completed_tutorials' => 0,
                'total_tutorials' => 0,
                'percentage' => 0,
            ];
        }
    }

    /**
     * Calculate user points based on activities.
     */
    private function calculateUserPoints($user): int
    {
        try {
            $points = 0;

            // Points pour les tutoriels complétés
            $completedTutorials = $user->tutorialProgress()
                ->where('completed', true)
                ->with('tutorial')
                ->get();

            foreach ($completedTutorials as $progress) {
                $tutorial = $progress->tutorial;
                if ($tutorial) {
                    switch ($tutorial->required_level) {
                        case 'beginner':
                            $points += 10;
                            break;
                        case 'intermediate':
                            $points += 20;
                            break;
                        case 'expert':
                            $points += 50;
                            break;
                        default:
                            $points += 15;
                    }
                }
            }

            // Points pour les badges
            $points += $this->safeCount($user->badges()) * 25;

            // Points pour les téléchargements
            $points += $this->safeCount($user->downloads()) * 2;

            return $points;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get next level for progression.
     */
    private function getNextLevel(string $currentLevel): ?string
    {
        $levels = ['beginner', 'intermediate', 'expert'];
        $currentIndex = array_search($currentLevel, $levels);
        
        return $currentIndex !== false && $currentIndex < count($levels) - 1 
            ? $levels[$currentIndex + 1] 
            : null;
    }

    /**
     * Get recent achievements.
     */
    private function getRecentAchievements($user): array
    {
        try {
            $achievements = [];

            // Badges récents
            $recentBadges = $this->getRecentBadges($user);
            foreach ($recentBadges as $badge) {
                $achievements[] = [
                    'type' => 'badge',
                    'title' => 'Nouveau badge : ' . $badge->name,
                    'date' => $badge->pivot->created_at,
                ];
            }

            // Tutoriels complétés récemment
            $recentCompletions = $user->tutorialProgress()
                ->where('completed', true)
                ->where('updated_at', '>=', Carbon::now()->subDays(7))
                ->with('tutorial')
                ->get();

            foreach ($recentCompletions as $progress) {
                if ($progress->tutorial) {
                    $achievements[] = [
                        'type' => 'completion',
                        'title' => 'Tutoriel terminé : ' . $progress->tutorial->title,
                        'date' => $progress->updated_at,
                    ];
                }
            }

            return collect($achievements)
                ->sortByDesc('date')
                ->take(5)
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Calculate time spent on platform.
     */
    private function calculateTimeSpent($user, int $days): int
    {
        try {
            // Estimation basée sur les tutoriels complétés et leur durée
            $completedTutorials = $user->tutorialProgress()
                ->where('completed', true)
                ->where('updated_at', '>=', Carbon::now()->subDays($days))
                ->with('tutorial')
                ->get();

            $totalMinutes = 0;
            foreach ($completedTutorials as $progress) {
                if ($progress->tutorial) {
                    $totalMinutes += 30; // 30 min par défaut par tutoriel
                }
            }

            return $totalMinutes;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
