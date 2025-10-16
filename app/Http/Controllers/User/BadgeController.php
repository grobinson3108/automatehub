<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\BadgeService;
use App\Services\AnalyticsService;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class BadgeController extends Controller
{
    protected BadgeService $badgeService;
    protected AnalyticsService $analyticsService;

    public function __construct(
        BadgeService $badgeService,
        AnalyticsService $analyticsService
    ) {
        $this->badgeService = $badgeService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display user's badges and progression.
     */
    public function index()
    {
        $user = auth()->user();

        // Badges obtenus par l'utilisateur
        $earnedBadges = $user->badges()
            ->withPivot('created_at')
            ->orderByPivot('created_at', 'desc')
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color,
                    'category' => $badge->category,
                    'points' => $badge->points,
                    'earned_at' => $badge->pivot->created_at,
                    'days_ago' => $badge->pivot->created_at->diffInDays(now()),
                ];
            });

        // Badges disponibles à débloquer
        $badgesData = $this->badgeService->getAvailableBadges($user->id);
        $availableBadges = collect($badgesData['available'])
            ->map(function ($badge) use ($user) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color ?? null,
                    'category' => $badge->category ?? null,
                    'points' => $badge->points ?? 0,
                    'requirements' => $badge->requirements ?? [],
                    'progress' => $badge->progress ?? null,
                ];
            });

        // Statistiques des badges
        $badgeStats = [
            'total_earned' => $earnedBadges->count(),
            'total_available' => Badge::count(),
            'completion_percentage' => Badge::count() > 0 ? 
                round(($earnedBadges->count() / Badge::count()) * 100, 1) : 0,
            'total_points' => $earnedBadges->sum('points'),
            'recent_badges' => $earnedBadges->where('days_ago', '<=', 30)->count(),
        ];

        // Badges par catégorie
        $badgesByCategory = $earnedBadges->groupBy('category');
        $availableByCategory = $availableBadges->groupBy('category');

        // Progression vers les prochains badges
        $nextBadges = $availableBadges
            ->filter(function ($badge) {
                return isset($badge['progress']) && $badge['progress']['percentage'] > 0;
            })
            ->sortByDesc('progress.percentage')
            ->take(3);

        // Badges récents (30 derniers jours)
        $recentBadges = $earnedBadges->where('days_ago', '<=', 30);

        // Tracking
        $this->analyticsService->track($user->id, 'badges_page_viewed', [
            'total_earned' => $badgeStats['total_earned'],
            'completion_percentage' => $badgeStats['completion_percentage'],
        ]);

        return view('user.badges.index', compact(
            'earnedBadges',
            'availableBadges',
            'badgeStats',
            'badgesByCategory',
            'availableByCategory',
            'nextBadges',
            'recentBadges'
        ));
    }

    /**
     * Display details of a specific badge.
     */
    public function show($id)
    {
        $user = auth()->user();
        $badge = Badge::findOrFail($id);

        // Vérifier si l'utilisateur a ce badge
        $userBadge = $user->badges()->where('badge_id', $id)->first();
        $hasEarned = $userBadge !== null;

        // Informations du badge
        $badgeInfo = [
            'id' => $badge->id,
            'name' => $badge->name,
            'description' => $badge->description,
            'icon' => $badge->icon,
            'color' => $badge->color,
            'category' => $badge->category,
            'points' => $badge->points,
            'requirements' => $badge->requirements,
            'rarity' => $this->calculateBadgeRarity($badge),
            'has_earned' => $hasEarned,
            'earned_at' => $hasEarned ? $userBadge->pivot->created_at : null,
        ];

        // Progression vers ce badge (si pas encore obtenu)
        $progress = null;
        if (!$hasEarned) {
            $progress = $this->badgeService->getBadgeProgress($user->id, $badge->id);
        }

        // Statistiques du badge
        $badgeStats = [
            'total_users_earned' => $badge->users()->count(),
            'percentage_of_users' => $this->calculateUserPercentage($badge),
            'average_time_to_earn' => $this->calculateAverageTimeToEarn($badge),
            'first_earned_by' => $this->getFirstUserToEarn($badge),
            'recently_earned_by' => $this->getRecentlyEarnedBy($badge),
        ];

        // Badges similaires ou de la même catégorie
        $relatedBadges = Badge::where('category', $badge->category)
            ->where('id', '!=', $badge->id)
            ->limit(4)
            ->get();

        // Tracking
        $this->analyticsService->track($user->id, 'badge_viewed', [
            'badge_id' => $badge->id,
            'badge_name' => $badge->name,
            'has_earned' => $hasEarned,
        ]);

        return view('user.badges.show', compact(
            'badgeInfo',
            'progress',
            'badgeStats',
            'relatedBadges'
        ));
    }

    /**
     * Get available badges to unlock.
     */
    public function getAvailable(): JsonResponse
    {
        $user = auth()->user();

        $badgesData = $this->badgeService->getAvailableBadges($user->id);
        $availableBadges = collect($badgesData['available'])
            ->map(function ($badge) use ($user) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'color' => $badge->color ?? null,
                    'category' => $badge->category ?? null,
                    'points' => $badge->points ?? 0,
                    'requirements' => $badge->requirements ?? [],
                    'progress' => $badge->progress ?? null,
                    'rarity' => $this->calculateBadgeRarity($badge),
                ];
            });

        return response()->json($availableBadges);
    }

    /**
     * Get user's badge statistics.
     */
    public function getStats(): JsonResponse
    {
        $user = auth()->user();

        // Badges obtenus
        $earnedBadges = $user->badges()->withPivot('created_at')->get();
        $totalBadges = Badge::count();

        // Statistiques générales
        $stats = [
            'total_earned' => $earnedBadges->count(),
            'total_available' => $totalBadges,
            'completion_percentage' => $totalBadges > 0 ? 
                round(($earnedBadges->count() / $totalBadges) * 100, 1) : 0,
            'total_points' => $earnedBadges->sum('points'),
        ];

        // Badges par catégorie
        $categoryStats = Badge::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get()
            ->map(function ($category) use ($user) {
                $earned = $user->badges()
                    ->where('category', $category->category)
                    ->count();
                
                return [
                    'category' => $category->category,
                    'total' => $category->total,
                    'earned' => $earned,
                    'percentage' => $category->total > 0 ? 
                        round(($earned / $category->total) * 100, 1) : 0,
                ];
            });

        // Progression récente (30 derniers jours)
        $recentProgress = [
            'badges_earned' => $earnedBadges
                ->where('pivot.created_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'points_earned' => $earnedBadges
                ->where('pivot.created_at', '>=', Carbon::now()->subDays(30))
                ->sum('points'),
        ];

        // Évolution des badges (6 derniers mois)
        $badgeEvolution = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = $earnedBadges
                ->where('pivot.created_at', '<=', $date->endOfMonth())
                ->count();
            
            $badgeEvolution[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }

        // Prochains badges à débloquer
        $badgesData = $this->badgeService->getAvailableBadges($user->id);
        $nextBadges = collect($badgesData['available'])
            ->map(function ($badge) use ($user) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'progress' => $badge->progress ?? null,
                ];
            })
            ->filter(function ($badge) {
                return isset($badge['progress']) && $badge['progress']['percentage'] > 0;
            })
            ->sortByDesc('progress.percentage')
            ->take(5)
            ->values();

        return response()->json([
            'general' => $stats,
            'by_category' => $categoryStats,
            'recent_progress' => $recentProgress,
            'evolution' => $badgeEvolution,
            'next_badges' => $nextBadges,
        ]);
    }

    /**
     * Get badge leaderboard.
     */
    public function getLeaderboard(): JsonResponse
    {
        // Top utilisateurs par nombre de badges
        $topByCount = \DB::table('user_badges')
            ->select('user_id', \DB::raw('COUNT(*) as badge_count'))
            ->join('users', 'user_badges.user_id', '=', 'users.id')
            ->groupBy('user_id')
            ->orderBy('badge_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $user = \App\Models\User::find($item->user_id);
                return [
                    'user_name' => $user->name,
                    'badge_count' => $item->badge_count,
                    'is_current_user' => $user->id === auth()->id(),
                ];
            });

        // Top utilisateurs par points de badges
        $topByPoints = \DB::table('user_badges')
            ->select('user_id', \DB::raw('SUM(badges.points) as total_points'))
            ->join('badges', 'user_badges.badge_id', '=', 'badges.id')
            ->join('users', 'user_badges.user_id', '=', 'users.id')
            ->groupBy('user_id')
            ->orderBy('total_points', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $user = \App\Models\User::find($item->user_id);
                return [
                    'user_name' => $user->name,
                    'total_points' => $item->total_points,
                    'is_current_user' => $user->id === auth()->id(),
                ];
            });

        // Position de l'utilisateur actuel
        $currentUser = auth()->user();
        $userRankByCount = \DB::table('user_badges')
            ->select('user_id', \DB::raw('COUNT(*) as badge_count'))
            ->groupBy('user_id')
            ->having('badge_count', '>', $currentUser->badges()->count())
            ->count() + 1;

        $userTotalPoints = $currentUser->badges()->sum('points');
        $userRankByPoints = \DB::table('user_badges')
            ->select('user_id', \DB::raw('SUM(badges.points) as total_points'))
            ->join('badges', 'user_badges.badge_id', '=', 'badges.id')
            ->groupBy('user_id')
            ->having('total_points', '>', $userTotalPoints)
            ->count() + 1;

        return response()->json([
            'top_by_count' => $topByCount,
            'top_by_points' => $topByPoints,
            'current_user_rank' => [
                'by_count' => $userRankByCount,
                'by_points' => $userRankByPoints,
                'badge_count' => $currentUser->badges()->count(),
                'total_points' => $userTotalPoints,
            ],
        ]);
    }

    /**
     * Get badge progress for a specific badge.
     */
    public function getProgress($id): JsonResponse
    {
        $user = auth()->user();
        $badge = Badge::findOrFail($id);

        // Vérifier si déjà obtenu
        if ($user->badges()->where('badge_id', $id)->exists()) {
            return response()->json([
                'earned' => true,
                'earned_at' => $user->badges()->where('badge_id', $id)->first()->pivot->created_at,
            ]);
        }

        // Obtenir la progression
        $progress = $this->badgeService->getBadgeProgress($user->id, $badge->id);

        return response()->json([
            'earned' => false,
            'progress' => $progress,
            'badge' => [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'requirements' => $badge->requirements,
                'points' => $badge->points,
            ],
        ]);
    }

    /**
     * Calculate badge rarity based on how many users have it.
     */
    private function calculateBadgeRarity(Badge $badge): string
    {
        $totalUsers = \App\Models\User::count();
        $usersWithBadge = $badge->users()->count();
        
        if ($totalUsers === 0) {
            return 'unknown';
        }

        $percentage = ($usersWithBadge / $totalUsers) * 100;

        if ($percentage >= 50) {
            return 'common';
        } elseif ($percentage >= 20) {
            return 'uncommon';
        } elseif ($percentage >= 5) {
            return 'rare';
        } elseif ($percentage >= 1) {
            return 'epic';
        } else {
            return 'legendary';
        }
    }

    /**
     * Calculate percentage of users who have this badge.
     */
    private function calculateUserPercentage(Badge $badge): float
    {
        $totalUsers = \App\Models\User::count();
        $usersWithBadge = $badge->users()->count();
        
        return $totalUsers > 0 ? round(($usersWithBadge / $totalUsers) * 100, 2) : 0;
    }

    /**
     * Calculate average time to earn this badge.
     */
    private function calculateAverageTimeToEarn(Badge $badge): ?int
    {
        $userBadges = $badge->users()
            ->withPivot('created_at')
            ->get();

        if ($userBadges->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        foreach ($userBadges as $userBadge) {
            $user = $userBadge;
            $earnedAt = $userBadge->pivot->created_at;
            $registeredAt = $user->created_at;
            $totalDays += $registeredAt->diffInDays($earnedAt);
        }

        return round($totalDays / $userBadges->count());
    }

    /**
     * Get the first user to earn this badge.
     */
    private function getFirstUserToEarn(Badge $badge): ?array
    {
        $firstUser = $badge->users()
            ->withPivot('created_at')
            ->orderByPivot('created_at', 'asc')
            ->first();

        if (!$firstUser) {
            return null;
        }

        return [
            'name' => $firstUser->name,
            'earned_at' => $firstUser->pivot->created_at,
        ];
    }

    /**
     * Get users who recently earned this badge.
     */
    private function getRecentlyEarnedBy(Badge $badge): array
    {
        return $badge->users()
            ->withPivot('created_at')
            ->where('user_badges.created_at', '>=', Carbon::now()->subDays(30))
            ->orderByPivot('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'earned_at' => $user->pivot->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Display user's n8n level and progression.
     */
    public function level()
    {
        $user = auth()->user();
        
        // Niveau n8n actuel de l'utilisateur
        $currentLevel = $user->n8n_level ?? 'beginner';
        
        // Progression n8n calculée par le service
        $n8nProgress = $this->badgeService->calculateN8nProgress($user->id);
        
        // Badges liés au niveau n8n
        $n8nBadges = Badge::where('type', 'n8n_level')->get();
        
        // Badges obtenus par l'utilisateur
        $earnedBadges = $user->badges()
            ->where('type', 'n8n_level')
            ->get();
        
        // Tracking
        $this->analyticsService->track($user->id, 'level_page_viewed', [
            'current_level' => $currentLevel,
            'overall_progress' => $n8nProgress['overall']['percentage'],
        ]);
        
        return view('user.level.index', compact(
            'currentLevel',
            'n8nProgress',
            'n8nBadges',
            'earnedBadges'
        ));
    }

    /**
     * Display n8n level quiz.
     */
    public function quiz()
    {
        $user = auth()->user();
        
        // Tracking
        $this->analyticsService->track($user->id, 'quiz_page_viewed', []);
        
        return view('user.level.quiz');
    }

    /**
     * Submit n8n level quiz.
     */
    public function submitQuiz(Request $request)
    {
        $user = auth()->user();
        
        // Validation des réponses
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);
        
        // Calcul du score et détermination du niveau
        $score = 0;
        $totalQuestions = count($validated['answers']);
        
        // Logique simplifiée pour déterminer le niveau
        // Dans une implémentation réelle, vous auriez une logique plus complexe
        // pour évaluer les réponses par rapport aux réponses correctes
        foreach ($validated['answers'] as $questionId => $answer) {
            // Simuler l'évaluation des réponses
            // Dans une vraie implémentation, vous compareriez avec les bonnes réponses
            if ($answer === 'correct_option') {
                $score++;
            }
        }
        
        $percentage = ($score / $totalQuestions) * 100;
        
        // Déterminer le niveau en fonction du score
        $newLevel = 'beginner';
        if ($percentage >= 80) {
            $newLevel = 'advanced';
        } elseif ($percentage >= 50) {
            $newLevel = 'intermediate';
        }
        
        // Mettre à jour le niveau de l'utilisateur
        $user->n8n_level = $newLevel;
        $user->save();
        
        // Vérifier si des badges peuvent être attribués
        $awardedBadges = $this->badgeService->checkAndAwardBadges($user->id);
        
        // Tracking
        $this->analyticsService->track($user->id, 'quiz_completed', [
            'score' => $score,
            'percentage' => $percentage,
            'new_level' => $newLevel,
            'badges_awarded' => count($awardedBadges),
        ]);
        
        return redirect()->route('user.level.index')
            ->with('success', "Félicitations ! Votre niveau n8n est maintenant : $newLevel");
    }

    /**
     * Display user's n8n progression.
     */
    public function progression()
    {
        $user = auth()->user();
        
        // Progression n8n détaillée
        $n8nProgress = $this->badgeService->calculateN8nProgress($user->id);
        
        // Tutoriels complétés par niveau
        $completedTutorials = \DB::table('user_tutorial_progress')
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->join('tutorials', 'user_tutorial_progress.tutorial_id', '=', 'tutorials.id')
            ->select('tutorials.*', 'user_tutorial_progress.completed_at')
            ->orderBy('user_tutorial_progress.completed_at', 'desc')
            ->get()
            ->groupBy('level');
        
        // Tracking
        $this->analyticsService->track($user->id, 'progression_page_viewed', [
            'current_level' => $user->n8n_level,
            'overall_progress' => $n8nProgress['overall']['percentage'],
        ]);
        
        return view('user.level.progression', compact(
            'n8nProgress',
            'completedTutorials'
        ));
    }
}
