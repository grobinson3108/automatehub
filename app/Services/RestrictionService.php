<?php

namespace App\Services;

use App\Models\User;
use App\Models\Download;
use App\Models\Tutorial;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RestrictionService
{
    // Limites par défaut selon les nouvelles spécifications
    const FREE_DOWNLOADS_PER_MONTH = 3;
    const FREE_DOWNLOADS_PER_DAY = 10;
    const PREMIUM_DOWNLOADS_PER_MONTH = 500;
    const PREMIUM_DOWNLOADS_PER_DAY = 50;
    const PREMIUM_UNLIMITED = true;
    const PRO_UNLIMITED = true;

    /**
     * Vérifie si un utilisateur peut télécharger
     */
    public function canDownload($userId)
    {
        $user = User::findOrFail($userId);

        // Les utilisateurs pro ont un accès illimité
        if ($user->subscription_type === 'pro') {
            return [
                'can_download' => true,
                'reason' => 'Accès illimité avec votre abonnement Pro'
            ];
        }

        // Vérifier les limites selon le type d'abonnement
        $limits = $this->getDownloadLimits($user->subscription_type);
        $usage = $this->getDownloadUsage($userId);

        // Vérifier la limite quotidienne
        if ($usage['today'] >= $limits['daily']) {
            return [
                'can_download' => false,
                'reason' => 'Limite quotidienne atteinte',
                'limit_type' => 'daily',
                'current_usage' => $usage['today'],
                'limit' => $limits['daily'],
                'reset_time' => Carbon::tomorrow()->startOfDay()
            ];
        }

        // Vérifier la limite mensuelle
        if ($usage['this_month'] >= $limits['monthly']) {
            return [
                'can_download' => false,
                'reason' => 'Limite mensuelle atteinte',
                'limit_type' => 'monthly',
                'current_usage' => $usage['this_month'],
                'limit' => $limits['monthly'],
                'reset_time' => Carbon::now()->addMonth()->startOfMonth()
            ];
        }

        return [
            'can_download' => true,
            'reason' => 'Téléchargement autorisé',
            'remaining_today' => $limits['daily'] - $usage['today'],
            'remaining_this_month' => $limits['monthly'] - $usage['this_month']
        ];
    }

    /**
     * Récupère le nombre de téléchargements restants pour un utilisateur
     */
    public function getRemainingDownloads($userId)
    {
        $user = User::findOrFail($userId);

        // Les utilisateurs pro ont un accès illimité
        if ($user->subscription_type === 'pro') {
            return [
                'daily' => [
                    'remaining' => 'illimité',
                    'total' => 'illimité',
                    'used' => $this->getDownloadUsage($userId)['today']
                ],
                'monthly' => [
                    'remaining' => 'illimité',
                    'total' => 'illimité',
                    'used' => $this->getDownloadUsage($userId)['this_month']
                ]
            ];
        }

        $limits = $this->getDownloadLimits($user->subscription_type);
        $usage = $this->getDownloadUsage($userId);

        return [
            'daily' => [
                'remaining' => max(0, $limits['daily'] - $usage['today']),
                'total' => $limits['daily'],
                'used' => $usage['today'],
                'reset_time' => Carbon::tomorrow()->startOfDay()
            ],
            'monthly' => [
                'remaining' => max(0, $limits['monthly'] - $usage['this_month']),
                'total' => $limits['monthly'],
                'used' => $usage['this_month'],
                'reset_time' => Carbon::now()->addMonth()->startOfMonth()
            ]
        ];
    }

    /**
     * Vérifie si un utilisateur peut accéder au contenu premium
     */
    public function canAccessPremiumContent($userId)
    {
        $user = User::findOrFail($userId);

        $hasAccess = in_array($user->subscription_type, ['premium', 'pro']);

        return [
            'has_access' => $hasAccess,
            'subscription_type' => $user->subscription_type,
            'message' => $hasAccess 
                ? 'Accès autorisé au contenu premium'
                : 'Abonnement Premium ou Pro requis pour accéder à ce contenu'
        ];
    }

    /**
     * Vérifie si un utilisateur peut accéder au contenu premium
     */
    public function canAccessPremium($user)
    {
        if (is_numeric($user)) {
            $user = User::findOrFail($user);
        }

        return in_array($user->subscription_type, ['premium', 'pro']);
    }

    /**
     * Vérifie si un utilisateur peut accéder au contenu pro
     */
    public function canAccessPro($user)
    {
        if (is_numeric($user)) {
            $user = User::findOrFail($user);
        }

        return $user->subscription_type === 'pro';
    }

    /**
     * Génère des messages d'incitation à l'upgrade
     */
    public function upgradePrompts($userId)
    {
        $user = User::findOrFail($userId);
        $prompts = [];

        switch ($user->subscription_type) {
            case 'free':
                $prompts = $this->getFreeUserPrompts($userId);
                break;
            case 'premium':
                $prompts = $this->getPremiumUserPrompts($userId);
                break;
            case 'pro':
                // Les utilisateurs pro n'ont pas besoin de prompts d'upgrade
                $prompts = [];
                break;
        }

        return $prompts;
    }

    /**
     * Vérifie si un utilisateur a atteint ses limites et doit être incité à upgrader
     */
    public function shouldShowUpgradePrompt($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->subscription_type === 'pro') {
            return false;
        }

        $usage = $this->getDownloadUsage($userId);
        $limits = $this->getDownloadLimits($user->subscription_type);

        // Afficher le prompt si l'utilisateur a utilisé 80% de ses limites
        $dailyUsagePercent = ($usage['today'] / $limits['daily']) * 100;
        $monthlyUsagePercent = ($usage['this_month'] / $limits['monthly']) * 100;

        return $dailyUsagePercent >= 80 || $monthlyUsagePercent >= 80;
    }

    /**
     * Enregistre un téléchargement et vérifie les limites
     */
    public function recordDownload($userId, $tutorialId, $fileName)
    {
        $canDownload = $this->canDownload($userId);

        if (!$canDownload['can_download']) {
            throw new \Exception($canDownload['reason']);
        }

        // Enregistrer le téléchargement
        $download = Download::create([
            'user_id' => $userId,
            'tutorial_id' => $tutorialId,
            'file_name' => $fileName,
            'file_size' => 0, // À définir selon le fichier
            'downloaded_at' => now()
        ]);

        // Enregistrer l'analytics
        app(AnalyticsService::class)->trackDownload($userId, $tutorialId, $fileName);

        // Vérifier les badges
        app(BadgeService::class)->checkAndAwardBadges($userId);

        return $download;
    }

    /**
     * Récupère les limites de téléchargement selon le type d'abonnement
     */
    private function getDownloadLimits($subscriptionType)
    {
        switch ($subscriptionType) {
            case 'free':
                return [
                    'daily' => PHP_INT_MAX, // Pas de limite quotidienne pour free
                    'monthly' => self::FREE_DOWNLOADS_PER_MONTH
                ];
            case 'premium':
                return [
                    'daily' => PHP_INT_MAX, // Téléchargements illimités
                    'monthly' => PHP_INT_MAX
                ];
            case 'pro':
                return [
                    'daily' => PHP_INT_MAX,
                    'monthly' => PHP_INT_MAX
                ];
            default:
                return [
                    'daily' => PHP_INT_MAX,
                    'monthly' => self::FREE_DOWNLOADS_PER_MONTH
                ];
        }
    }

    /**
     * Récupère la limite de téléchargement pour un utilisateur
     */
    public function getDownloadLimit($user)
    {
        $subscriptionType = $user->subscription_type;
        
        switch ($subscriptionType) {
            case 'free':
                return self::FREE_DOWNLOADS_PER_MONTH;
            case 'premium':
            case 'pro':
                return PHP_INT_MAX; // Illimité
            default:
                return self::FREE_DOWNLOADS_PER_MONTH;
        }
    }

    /**
     * Récupère l'utilisation actuelle des téléchargements
     */
    private function getDownloadUsage($userId)
    {
        // Utiliser le cache pour optimiser les performances
        $cacheKey = "download_usage_{$userId}";
        
        return Cache::remember($cacheKey, 300, function () use ($userId) { // Cache 5 minutes
            return [
                'today' => Download::where('user_id', $userId)
                    ->whereDate('downloaded_at', today())
                    ->count(),
                'this_month' => Download::where('user_id', $userId)
                    ->whereMonth('downloaded_at', now()->month)
                    ->whereYear('downloaded_at', now()->year)
                    ->count(),
                'total' => Download::where('user_id', $userId)->count()
            ];
        });
    }

    /**
     * Messages d'incitation pour les utilisateurs free
     */
    private function getFreeUserPrompts($userId)
    {
        $usage = $this->getDownloadUsage($userId);
        $limits = $this->getDownloadLimits('free');
        $prompts = [];

        // Prompt basé sur l'utilisation quotidienne
        if ($usage['today'] >= $limits['daily']) {
            $prompts[] = [
                'type' => 'daily_limit_reached',
                'title' => 'Limite quotidienne atteinte !',
                'message' => 'Vous avez atteint votre limite de ' . $limits['daily'] . ' téléchargements par jour.',
                'cta' => 'Passez à Premium pour 50 téléchargements/jour',
                'urgency' => 'high',
                'benefits' => [
                    '50 téléchargements par jour',
                    'Accès aux tutoriels premium',
                    'Support prioritaire'
                ]
            ];
        } elseif ($usage['today'] >= ($limits['daily'] * 0.8)) {
            $prompts[] = [
                'type' => 'daily_limit_warning',
                'title' => 'Bientôt à court de téléchargements',
                'message' => 'Il vous reste ' . ($limits['daily'] - $usage['today']) . ' téléchargements aujourd\'hui.',
                'cta' => 'Découvrir Premium',
                'urgency' => 'medium'
            ];
        }

        // Prompt basé sur l'utilisation mensuelle
        if ($usage['this_month'] >= $limits['monthly']) {
            $prompts[] = [
                'type' => 'monthly_limit_reached',
                'title' => 'Limite mensuelle atteinte !',
                'message' => 'Vous avez utilisé vos ' . $limits['monthly'] . ' téléchargements du mois.',
                'cta' => 'Upgrader maintenant',
                'urgency' => 'high'
            ];
        } elseif ($usage['this_month'] >= ($limits['monthly'] * 0.8)) {
            $prompts[] = [
                'type' => 'monthly_limit_warning',
                'title' => 'Attention aux limites mensuelles',
                'message' => 'Vous avez utilisé ' . $usage['this_month'] . '/' . $limits['monthly'] . ' téléchargements ce mois.',
                'cta' => 'Voir les options Premium',
                'urgency' => 'medium'
            ];
        }

        // Prompt général pour les utilisateurs actifs
        if ($usage['total'] >= 10) {
            $prompts[] = [
                'type' => 'active_user',
                'title' => 'Vous êtes un utilisateur actif !',
                'message' => 'Avec ' . $usage['total'] . ' téléchargements, vous pourriez bénéficier de Premium.',
                'cta' => 'Découvrir les avantages Premium',
                'urgency' => 'low',
                'benefits' => [
                    'Téléchargements illimités',
                    'Contenu exclusif',
                    'Pas de publicité'
                ]
            ];
        }

        return $prompts;
    }

    /**
     * Messages d'incitation pour les utilisateurs premium
     */
    private function getPremiumUserPrompts($userId)
    {
        $usage = $this->getDownloadUsage($userId);
        $prompts = [];

        // Inciter à passer Pro si utilisation intensive
        if ($usage['this_month'] >= 400) { // 80% de la limite premium
            $prompts[] = [
                'type' => 'heavy_user',
                'title' => 'Utilisateur intensif détecté',
                'message' => 'Avec ' . $usage['this_month'] . ' téléchargements ce mois, Pro pourrait vous convenir.',
                'cta' => 'Découvrir Pro',
                'urgency' => 'medium',
                'benefits' => [
                    'Téléchargements illimités',
                    'Tutoriels sur demande',
                    'Support dédié',
                    'Accès API'
                ]
            ];
        }

        // Prompt pour les fonctionnalités Pro exclusives
        $prompts[] = [
            'type' => 'pro_features',
            'title' => 'Fonctionnalités Pro disponibles',
            'message' => 'Débloquez les tutoriels sur demande et l\'accès API.',
            'cta' => 'Upgrader vers Pro',
            'urgency' => 'low'
        ];

        return $prompts;
    }

    /**
     * Vérifie les restrictions d'accès pour un contenu spécifique
     */
    public function checkContentAccess($userId, $contentType, $contentId = null)
    {
        $user = User::findOrFail($userId);

        switch ($contentType) {
            case 'tutorial':
                return $this->checkTutorialAccess($user, $contentId);
            case 'download':
                return $this->canDownload($userId);
            case 'premium_content':
                return $this->canAccessPremiumContent($userId);
            default:
                return ['has_access' => false, 'reason' => 'Type de contenu non reconnu'];
        }
    }

    /**
     * Vérifie si un utilisateur peut accéder à un tutoriel spécifique
     */
    public function canAccessTutorial($user, $tutorial)
    {
        if (!$tutorial) {
            return false;
        }

        if (!$tutorial->isPublished()) {
            return false;
        }

        // Vérifier les restrictions d'abonnement
        switch ($tutorial->subscription_required) {
            case 'free':
                return true;
            case 'premium':
                return in_array($user->subscription_type, ['premium', 'pro']);
            case 'pro':
                return $user->subscription_type === 'pro';
            default:
                return false;
        }
    }

    /**
     * Vérifie l'accès à un tutoriel spécifique
     */
    private function checkTutorialAccess(User $user, $tutorialId)
    {
        if (!$tutorialId) {
            return ['has_access' => false, 'reason' => 'ID du tutoriel manquant'];
        }

        $tutorial = Tutorial::find($tutorialId);
        if (!$tutorial) {
            return ['has_access' => false, 'reason' => 'Tutoriel introuvable'];
        }

        if (!$tutorial->is_published) {
            return ['has_access' => false, 'reason' => 'Tutoriel non publié'];
        }

        // Vérifier les restrictions d'abonnement
        switch ($tutorial->subscription_required) {
            case 'free':
                return ['has_access' => true, 'reason' => 'Contenu gratuit'];
            case 'premium':
                $hasAccess = in_array($user->subscription_type, ['premium', 'pro']);
                return [
                    'has_access' => $hasAccess,
                    'reason' => $hasAccess ? 'Accès Premium autorisé' : 'Abonnement Premium requis'
                ];
            case 'pro':
                $hasAccess = $user->subscription_type === 'pro';
                return [
                    'has_access' => $hasAccess,
                    'reason' => $hasAccess ? 'Accès Pro autorisé' : 'Abonnement Pro requis'
                ];
            default:
                return ['has_access' => false, 'reason' => 'Niveau d\'accès non défini'];
        }
    }

    /**
     * Invalide le cache d'utilisation après un téléchargement
     */
    public function clearUsageCache($userId)
    {
        Cache::forget("download_usage_{$userId}");
    }

    /**
     * Récupère les statistiques d'utilisation pour l'admin
     */
    public function getUsageStatistics()
    {
        $stats = [];

        // Utilisateurs par type d'abonnement
        $stats['users_by_subscription'] = [
            'free' => User::where('subscription_type', 'free')->count(),
            'premium' => User::where('subscription_type', 'premium')->count(),
            'pro' => User::where('subscription_type', 'pro')->count(),
        ];

        // Téléchargements par période
        $stats['downloads'] = [
            'today' => Download::whereDate('downloaded_at', today())->count(),
            'this_week' => Download::whereBetween('downloaded_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Download::whereMonth('downloaded_at', now()->month)->count(),
        ];

        // Utilisateurs ayant atteint leurs limites
        $stats['users_at_limits'] = [
            'daily' => $this->getUsersAtDailyLimit(),
            'monthly' => $this->getUsersAtMonthlyLimit(),
        ];

        return $stats;
    }

    /**
     * Compte les utilisateurs ayant atteint leur limite quotidienne
     */
    private function getUsersAtDailyLimit()
    {
        $freeLimit = self::FREE_DOWNLOADS_PER_DAY;
        $premiumLimit = self::PREMIUM_DOWNLOADS_PER_DAY;

        $freeUsersAtLimit = User::where('subscription_type', 'free')
            ->whereHas('downloads', function ($query) use ($freeLimit) {
                $query->whereDate('downloaded_at', today())
                      ->havingRaw('COUNT(*) >= ?', [$freeLimit]);
            }, '>=', $freeLimit)
            ->count();

        $premiumUsersAtLimit = User::where('subscription_type', 'premium')
            ->whereHas('downloads', function ($query) use ($premiumLimit) {
                $query->whereDate('downloaded_at', today())
                      ->havingRaw('COUNT(*) >= ?', [$premiumLimit]);
            }, '>=', $premiumLimit)
            ->count();

        return $freeUsersAtLimit + $premiumUsersAtLimit;
    }

    /**
     * Compte les utilisateurs ayant atteint leur limite mensuelle
     */
    private function getUsersAtMonthlyLimit()
    {
        $freeLimit = self::FREE_DOWNLOADS_PER_MONTH;
        $premiumLimit = self::PREMIUM_DOWNLOADS_PER_MONTH;

        $freeUsersAtLimit = User::where('subscription_type', 'free')
            ->whereHas('downloads', function ($query) use ($freeLimit) {
                $query->whereMonth('downloaded_at', now()->month)
                      ->whereYear('downloaded_at', now()->year)
                      ->havingRaw('COUNT(*) >= ?', [$freeLimit]);
            }, '>=', $freeLimit)
            ->count();

        $premiumUsersAtLimit = User::where('subscription_type', 'premium')
            ->whereHas('downloads', function ($query) use ($premiumLimit) {
                $query->whereMonth('downloaded_at', now()->month)
                      ->whereYear('downloaded_at', now()->year)
                      ->havingRaw('COUNT(*) >= ?', [$premiumLimit]);
            }, '>=', $premiumLimit)
            ->count();

        return $freeUsersAtLimit + $premiumUsersAtLimit;
    }
}
