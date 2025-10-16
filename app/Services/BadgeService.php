<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\Download;
use App\Models\Analytics;
use App\Models\UserTutorialProgress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeService
{
    /**
     * Vérifie et attribue automatiquement les badges à un utilisateur
     */
    public function checkAndAwardBadges($userId)
    {
        $user = User::findOrFail($userId);
        $awardedBadges = [];

        // Récupérer tous les badges disponibles
        $badges = Badge::where('is_active', 1)->get();

        foreach ($badges as $badge) {
            // Vérifier si l'utilisateur a déjà ce badge
            $hasAlreadyBadge = DB::table('user_badges')
                ->where('user_id', $userId)
                ->where('badge_id', $badge->id)
                ->exists();

            if (!$hasAlreadyBadge && $this->checkBadgeCondition($user, $badge)) {
                $this->awardBadge($userId, $badge->id);
                $awardedBadges[] = $badge;
            }
        }

        return $awardedBadges;
    }

    /**
     * Vérifie si un utilisateur remplit les conditions pour un badge
     */
    private function checkBadgeCondition(User $user, Badge $badge)
    {
        $conditions = json_decode($badge->conditions, true);

        switch ($badge->type) {
            case 'registration':
                return $this->checkRegistrationBadge($user, $conditions);
            
            case 'tutorial_completion':
                return $this->checkTutorialCompletionBadge($user, $conditions);
            
            case 'download':
                return $this->checkDownloadBadge($user, $conditions);
            
            case 'activity':
                return $this->checkActivityBadge($user, $conditions);
            
            case 'n8n_level':
                return $this->checkN8nLevelBadge($user, $conditions);
            
            case 'subscription':
                return $this->checkSubscriptionBadge($user, $conditions);
            
            case 'streak':
                return $this->checkStreakBadge($user, $conditions);
            
            case 'special':
                return $this->checkSpecialBadge($user, $conditions);
            
            default:
                return false;
        }
    }

    /**
     * Vérifie les badges d'inscription
     */
    private function checkRegistrationBadge(User $user, array $conditions)
    {
        if (isset($conditions['welcome']) && $conditions['welcome']) {
            return true; // Badge de bienvenue automatique
        }

        if (isset($conditions['professional']) && $conditions['professional']) {
            return $user->is_professional;
        }

        if (isset($conditions['quiz_completed']) && $conditions['quiz_completed']) {
            return !is_null($user->n8n_level);
        }

        return false;
    }

    /**
     * Vérifie les badges de complétion de tutoriels
     */
    private function checkTutorialCompletionBadge(User $user, array $conditions)
    {
        $completedCount = UserTutorialProgress::where('user_id', $user->id)
            ->where('progress_percentage', '>=', 100)
            ->whereNotNull('completed_at')
            ->count();

        if (isset($conditions['count'])) {
            return $completedCount >= $conditions['count'];
        }

        if (isset($conditions['first_tutorial']) && $conditions['first_tutorial']) {
            return $completedCount >= 1;
        }

        if (isset($conditions['level'])) {
            $completedInLevel = UserTutorialProgress::where('user_id', $user->id)
                ->where('progress_percentage', '>=', 100)
                ->whereNotNull('completed_at')
                ->join('tutorials', 'user_tutorial_progress.tutorial_id', '=', 'tutorials.id')
                ->where('tutorials.required_level', $conditions['level'])
                ->count();
            
            return $completedInLevel >= ($conditions['count'] ?? 1);
        }

        return false;
    }

    /**
     * Vérifie les badges de téléchargement
     */
    private function checkDownloadBadge(User $user, array $conditions)
    {
        $downloadCount = Download::where('user_id', $user->id)->count();

        if (isset($conditions['count'])) {
            return $downloadCount >= $conditions['count'];
        }

        if (isset($conditions['first_download']) && $conditions['first_download']) {
            return $downloadCount >= 1;
        }

        return false;
    }

    /**
     * Vérifie les badges d'activité
     */
    private function checkActivityBadge(User $user, array $conditions)
    {
        if (isset($conditions['page_views'])) {
            $pageViews = Analytics::where('user_id', $user->id)
                ->where('event_type', 'page_view')
                ->count();
            
            return $pageViews >= $conditions['page_views'];
        }

        if (isset($conditions['tutorial_views'])) {
            $tutorialViews = Analytics::where('user_id', $user->id)
                ->where('event_type', 'tutorial_view')
                ->count();
            
            return $tutorialViews >= $conditions['tutorial_views'];
        }

        if (isset($conditions['days_active'])) {
            $activeDays = Analytics::where('user_id', $user->id)
                ->selectRaw('DATE(created_at) as date')
                ->distinct()
                ->count();
            
            return $activeDays >= $conditions['days_active'];
        }

        return false;
    }

    /**
     * Vérifie les badges de niveau n8n
     */
    private function checkN8nLevelBadge(User $user, array $conditions)
    {
        if (isset($conditions['level'])) {
            return $user->n8n_level === $conditions['level'];
        }

        return false;
    }

    /**
     * Vérifie les badges d'abonnement
     */
    private function checkSubscriptionBadge(User $user, array $conditions)
    {
        if (isset($conditions['type'])) {
            return $user->subscription_type === $conditions['type'];
        }

        if (isset($conditions['upgrade_to_premium']) && $conditions['upgrade_to_premium']) {
            return in_array($user->subscription_type, ['premium', 'pro']);
        }

        return false;
    }

    /**
     * Vérifie les badges de série (streak)
     */
    private function checkStreakBadge(User $user, array $conditions)
    {
        if (isset($conditions['consecutive_days'])) {
            $streak = $this->calculateCurrentStreak($user->id);
            return $streak >= $conditions['consecutive_days'];
        }

        return false;
    }

    /**
     * Vérifie les badges spéciaux
     */
    private function checkSpecialBadge(User $user, array $conditions)
    {
        if (isset($conditions['early_adopter']) && $conditions['early_adopter']) {
            // Badge pour les premiers utilisateurs (par exemple, les 100 premiers)
            $userRank = User::where('created_at', '<=', $user->created_at)->count();
            return $userRank <= 100;
        }

        if (isset($conditions['beta_tester']) && $conditions['beta_tester']) {
            // Badge pour les testeurs beta (à définir selon vos critères)
            return $user->created_at <= Carbon::parse('2025-12-31'); // Exemple
        }

        return false;
    }

    /**
     * Calcule la progression n8n d'un utilisateur
     */
    public function calculateN8nProgress($userId)
    {
        $user = User::findOrFail($userId);
        $progress = [];

        // Progression basée sur les tutoriels complétés par niveau
        $levels = ['beginner', 'intermediate', 'advanced'];
        
        foreach ($levels as $level) {
            $totalTutorials = DB::table('tutorials')
                ->where('required_level', $level)
                ->where('subscription_required', '<=', $this->getSubscriptionLevel($user->subscription_type))
                ->count();

            $completedTutorials = UserTutorialProgress::where('user_id', $userId)
                ->where('progress_percentage', '>=', 100)
                ->whereNotNull('completed_at')
                ->join('tutorials', 'user_tutorial_progress.tutorial_id', '=', 'tutorials.id')
                ->where('tutorials.required_level', $level)
                ->count();

            $progress[$level] = [
                'completed' => $completedTutorials,
                'total' => $totalTutorials,
                'percentage' => $totalTutorials > 0 ? round(($completedTutorials / $totalTutorials) * 100, 2) : 0
            ];
        }

        // Progression globale
        $totalCompleted = array_sum(array_column($progress, 'completed'));
        $totalAvailable = array_sum(array_column($progress, 'total'));
        
        $progress['overall'] = [
            'completed' => $totalCompleted,
            'total' => $totalAvailable,
            'percentage' => $totalAvailable > 0 ? round(($totalCompleted / $totalAvailable) * 100, 2) : 0
        ];

        // Suggestion de niveau suivant
        $progress['suggested_next_level'] = $this->suggestNextLevel($user, $progress);

        return $progress;
    }

    /**
     * Récupère les badges disponibles pour un utilisateur
     */
    public function getAvailableBadges($userId)
    {
        $user = User::findOrFail($userId);
        
        // Badges déjà obtenus
        $earnedBadgeIds = DB::table('user_badges')
            ->where('user_id', $userId)
            ->pluck('badge_id')
            ->toArray();

        // Tous les badges actifs
        $allBadges = Badge::where('is_active', 1)->get();

        $availableBadges = [];
        $earnedBadges = [];

        foreach ($allBadges as $badge) {
            if (in_array($badge->id, $earnedBadgeIds)) {
                $earnedBadges[] = $badge;
            } else {
                // Calculer la progression vers ce badge
                $progress = $this->calculateBadgeProgress($user, $badge);
                $badge->progress = $progress;
                $availableBadges[] = $badge;
            }
        }

        return [
            'earned' => $earnedBadges,
            'available' => $availableBadges
        ];
    }

    /**
     * Attribue manuellement un badge à un utilisateur
     */
    public function awardBadge($userId, $badgeId)
    {
        // Vérifier si l'utilisateur a déjà ce badge
        $exists = DB::table('user_badges')
            ->where('user_id', $userId)
            ->where('badge_id', $badgeId)
            ->exists();

        if (!$exists) {
            DB::table('user_badges')->insert([
                'user_id' => $userId,
                'badge_id' => $badgeId,
                'earned_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return true;
        }

        return false;
    }

    /**
     * Calcule la série actuelle d'activité d'un utilisateur
     */
    private function calculateCurrentStreak($userId)
    {
        $activities = Analytics::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($activities)) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($activities as $activityDate) {
            $activityCarbon = Carbon::parse($activityDate);
            
            if ($activityCarbon->equalTo($currentDate) || $activityCarbon->equalTo($currentDate->subDay())) {
                $streak++;
                $currentDate = $activityCarbon->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Convertit le type d'abonnement en niveau numérique
     */
    private function getSubscriptionLevel($subscriptionType)
    {
        switch ($subscriptionType) {
            case 'free':
                return 1;
            case 'premium':
                return 2;
            case 'pro':
                return 3;
            default:
                return 1;
        }
    }

    /**
     * Suggère le niveau suivant pour l'utilisateur
     */
    private function suggestNextLevel(User $user, array $progress)
    {
        $currentLevel = $user->n8n_level;
        
        if ($currentLevel === 'beginner' && $progress['beginner']['percentage'] >= 80) {
            return 'intermediate';
        }
        
        if ($currentLevel === 'intermediate' && $progress['intermediate']['percentage'] >= 80) {
            return 'advanced';
        }
        
        if ($currentLevel === 'advanced' && $progress['advanced']['percentage'] >= 90) {
            return 'expert';
        }

        return $currentLevel;
    }

    /**
     * Calcule la progression vers un badge spécifique
     */
    private function calculateBadgeProgress(User $user, Badge $badge)
    {
        $conditions = json_decode($badge->conditions, true);
        $progress = ['percentage' => 0, 'current' => 0, 'target' => 1];

        switch ($badge->type) {
            case 'tutorial_completion':
                if (isset($conditions['count'])) {
                    $current = UserTutorialProgress::where('user_id', $user->id)
                        ->where('progress_percentage', '>=', 100)
                        ->whereNotNull('completed_at')
                        ->count();
                    $progress = [
                        'current' => $current,
                        'target' => $conditions['count'],
                        'percentage' => min(100, round(($current / $conditions['count']) * 100, 2))
                    ];
                }
                break;

            case 'download':
                if (isset($conditions['count'])) {
                    $current = Download::where('user_id', $user->id)->count();
                    $progress = [
                        'current' => $current,
                        'target' => $conditions['count'],
                        'percentage' => min(100, round(($current / $conditions['count']) * 100, 2))
                    ];
                }
                break;

            case 'activity':
                if (isset($conditions['page_views'])) {
                    $current = Analytics::where('user_id', $user->id)
                        ->where('event_type', 'page_view')->count();
                    $progress = [
                        'current' => $current,
                        'target' => $conditions['page_views'],
                        'percentage' => min(100, round(($current / $conditions['page_views']) * 100, 2))
                    ];
                }
                break;
        }

        return $progress;
    }
}
