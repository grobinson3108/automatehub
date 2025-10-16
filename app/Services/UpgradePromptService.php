<?php

namespace App\Services;

use App\Models\User;
use App\Models\Analytics;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UpgradePromptService
{
    protected AnalyticsService $analyticsService;
    protected RestrictionService $restrictionService;

    public function __construct(
        AnalyticsService $analyticsService,
        RestrictionService $restrictionService
    ) {
        $this->analyticsService = $analyticsService;
        $this->restrictionService = $restrictionService;
    }

    /**
     * Récupère le message d'upgrade selon le contexte
     */
    public function getUpgradeMessage($userId, $context = 'general')
    {
        $user = User::findOrFail($userId);

        // Les utilisateurs Pro n'ont pas besoin d'upgrade
        if ($user->subscription_type === 'pro') {
            return null;
        }

        // Vérifier si on doit afficher un prompt
        if (!$this->shouldShowPrompt($userId, $context)) {
            return null;
        }

        $message = $this->generateContextualMessage($user, $context);
        
        // Tracker l'affichage du prompt
        $this->trackUpgradePrompt($userId, $context);

        return $message;
    }

    /**
     * Génère un message contextuel selon la situation
     */
    private function generateContextualMessage(User $user, $context)
    {
        $baseData = [
            'user_id' => $user->id,
            'current_subscription' => $user->subscription_type,
            'context' => $context,
            'timestamp' => now(),
        ];

        switch ($context) {
            case 'download_limit_reached':
                return $this->getDownloadLimitMessage($user, $baseData);
            
            case 'premium_content_blocked':
                return $this->getPremiumContentMessage($user, $baseData);
            
            case 'tutorial_view':
                return $this->getTutorialViewMessage($user, $baseData);
            
            case 'dashboard':
                return $this->getDashboardMessage($user, $baseData);
            
            case 'profile':
                return $this->getProfileMessage($user, $baseData);
            
            case 'search_results':
                return $this->getSearchResultsMessage($user, $baseData);
            
            case 'badge_earned':
                return $this->getBadgeEarnedMessage($user, $baseData);
            
            default:
                return $this->getGeneralMessage($user, $baseData);
        }
    }

    /**
     * Message quand la limite de téléchargement est atteinte
     */
    private function getDownloadLimitMessage(User $user, $baseData)
    {
        $usage = $this->restrictionService->getDownloadUsage($user->id);
        
        if ($user->subscription_type === 'free') {
            return array_merge($baseData, [
                'type' => 'download_limit',
                'urgency' => 'high',
                'title' => '🚫 Limite de téléchargement atteinte',
                'message' => 'Vous avez utilisé vos 3 téléchargements gratuits ce mois.',
                'description' => 'Passez à Premium pour des téléchargements illimités et accédez à tout notre contenu exclusif.',
                'cta_primary' => [
                    'text' => 'Passer à Premium',
                    'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                    'style' => 'btn-warning',
                ],
                'cta_secondary' => [
                    'text' => 'Voir les tarifs',
                    'url' => route('frontend.pricing'),
                    'style' => 'btn-outline-secondary',
                ],
                'benefits' => [
                    '✅ Téléchargements illimités',
                    '✅ Accès au contenu premium',
                    '✅ Support prioritaire',
                    '✅ Pas de publicité',
                ],
                'social_proof' => 'Rejoignez plus de 1000+ utilisateurs Premium',
                'discount' => $this->getActiveDiscount('premium'),
            ]);
        }

        return null; // Premium et Pro ont des téléchargements illimités
    }

    /**
     * Message quand l'accès au contenu premium est bloqué
     */
    private function getPremiumContentMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free') {
            return array_merge($baseData, [
                'type' => 'premium_content',
                'urgency' => 'medium',
                'title' => '🔒 Contenu Premium',
                'message' => 'Ce tutoriel est réservé aux abonnés Premium et Pro.',
                'description' => 'Débloquez l\'accès à tous nos tutoriels avancés et ressources exclusives.',
                'cta_primary' => [
                    'text' => 'Débloquer Premium',
                    'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                    'style' => 'btn-primary',
                ],
                'preview_available' => true,
                'preview_text' => 'Voir un aperçu gratuit',
                'benefits' => [
                    '🎯 Tutoriels avancés n8n',
                    '📁 Workflows prêts à l\'emploi',
                    '🎥 Vidéos exclusives',
                    '💬 Accès communauté privée',
                ],
                'testimonial' => [
                    'text' => 'Premium m\'a fait gagner des heures de développement !',
                    'author' => 'Marie D., Développeuse',
                ],
            ]);
        }

        return null;
    }

    /**
     * Message lors de la consultation d'un tutoriel
     */
    private function getTutorialViewMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free') {
            $viewCount = $this->getUserTutorialViews($user->id);
            
            if ($viewCount >= 5) { // Après 5 vues, proposer l'upgrade
                return array_merge($baseData, [
                    'type' => 'tutorial_engagement',
                    'urgency' => 'low',
                    'title' => '🎓 Vous êtes un apprenant actif !',
                    'message' => "Vous avez consulté {$viewCount} tutoriels.",
                    'description' => 'Maximisez votre apprentissage avec Premium.',
                    'cta_primary' => [
                        'text' => 'Découvrir Premium',
                        'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                        'style' => 'btn-success',
                    ],
                    'benefits' => [
                        '📚 Accès à tous les tutoriels',
                        '⬇️ Téléchargements illimités',
                        '🏆 Badges exclusifs',
                    ],
                    'progress_bar' => [
                        'current' => $viewCount,
                        'target' => 10,
                        'label' => 'tutoriels consultés',
                    ],
                ]);
            }
        }

        return null;
    }

    /**
     * Message sur le dashboard
     */
    private function getDashboardMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free') {
            $daysSinceRegistration = $user->created_at->diffInDays(now());
            
            if ($daysSinceRegistration >= 7) { // Après une semaine
                return array_merge($baseData, [
                    'type' => 'dashboard_retention',
                    'urgency' => 'low',
                    'title' => '🚀 Prêt pour la suite ?',
                    'message' => "Cela fait {$daysSinceRegistration} jours que vous nous avez rejoint !",
                    'description' => 'Il est temps de passer au niveau supérieur avec Premium.',
                    'cta_primary' => [
                        'text' => 'Voir Premium',
                        'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                        'style' => 'btn-primary',
                    ],
                    'stats' => [
                        'tutorials_viewed' => $this->getUserTutorialViews($user->id),
                        'downloads_used' => $this->restrictionService->getDownloadUsage($user->id)['this_month'],
                        'badges_earned' => $user->badges()->count(),
                    ],
                ]);
            }
        } elseif ($user->subscription_type === 'premium') {
            $monthsSincePremium = $user->updated_at->diffInMonths(now());
            
            if ($monthsSincePremium >= 3) { // Après 3 mois en Premium
                return array_merge($baseData, [
                    'type' => 'premium_to_pro',
                    'urgency' => 'low',
                    'title' => '⭐ Utilisateur Premium expérimenté',
                    'message' => 'Vous maîtrisez Premium depuis ' . $monthsSincePremium . ' mois.',
                    'description' => 'Découvrez les fonctionnalités Pro pour les experts.',
                    'cta_primary' => [
                        'text' => 'Découvrir Pro',
                        'url' => route('user.subscription.upgrade', ['plan' => 'pro']),
                        'style' => 'btn-dark',
                    ],
                    'pro_features' => [
                        '🎯 Tutoriels sur demande',
                        '🔧 API d\'intégration',
                        '👨‍💼 Support dédié',
                        '🏢 Fonctionnalités entreprise',
                    ],
                ]);
            }
        }

        return null;
    }

    /**
     * Message sur la page profil
     */
    private function getProfileMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free' && $user->is_professional) {
            return array_merge($baseData, [
                'type' => 'professional_upgrade',
                'urgency' => 'medium',
                'title' => '💼 Compte Professionnel détecté',
                'message' => 'Optimisez votre productivité avec Premium.',
                'description' => 'Les professionnels choisissent Premium pour ses fonctionnalités avancées.',
                'cta_primary' => [
                    'text' => 'Upgrade Professionnel',
                    'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                    'style' => 'btn-warning',
                ],
                'business_benefits' => [
                    '📊 Analytics avancées',
                    '🔄 Intégrations API',
                    '📞 Support prioritaire',
                    '🧾 Facturation entreprise',
                ],
                'roi_message' => 'ROI moyen : 300% en 3 mois',
            ]);
        }

        return null;
    }

    /**
     * Message dans les résultats de recherche
     */
    private function getSearchResultsMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free') {
            return array_merge($baseData, [
                'type' => 'search_results',
                'urgency' => 'low',
                'title' => '🔍 Résultats limités',
                'message' => 'Certains tutoriels premium n\'apparaissent pas dans vos résultats.',
                'description' => 'Accédez à toute notre bibliothèque avec Premium.',
                'cta_primary' => [
                    'text' => 'Voir tous les résultats',
                    'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                    'style' => 'btn-info',
                ],
                'hidden_count' => $this->getHiddenPremiumCount(),
            ]);
        }

        return null;
    }

    /**
     * Message quand un badge est gagné
     */
    private function getBadgeEarnedMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free') {
            $badgeCount = $user->badges()->count();
            
            if ($badgeCount >= 3) {
                return array_merge($baseData, [
                    'type' => 'badge_milestone',
                    'urgency' => 'low',
                    'title' => '🏆 Collectionneur de badges !',
                    'message' => "Félicitations ! Vous avez {$badgeCount} badges.",
                    'description' => 'Débloquez des badges exclusifs avec Premium.',
                    'cta_primary' => [
                        'text' => 'Badges Premium',
                        'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                        'style' => 'btn-success',
                    ],
                    'exclusive_badges' => [
                        '🥇 Expert n8n',
                        '⚡ Power User',
                        '🎯 Perfectionniste',
                    ],
                ]);
            }
        }

        return null;
    }

    /**
     * Message général par défaut
     */
    private function getGeneralMessage(User $user, $baseData)
    {
        if ($user->subscription_type === 'free') {
            return array_merge($baseData, [
                'type' => 'general',
                'urgency' => 'low',
                'title' => '✨ Découvrez Premium',
                'message' => 'Débloquez tout le potentiel d\'Automatehub.',
                'description' => 'Rejoignez des milliers d\'utilisateurs qui ont choisi Premium.',
                'cta_primary' => [
                    'text' => 'Essayer Premium',
                    'url' => route('user.subscription.upgrade', ['plan' => 'premium']),
                    'style' => 'btn-primary',
                ],
                'features' => [
                    'Contenu illimité',
                    'Support prioritaire',
                    'Fonctionnalités avancées',
                ],
            ]);
        }

        return null;
    }

    /**
     * Vérifie si on doit afficher un prompt
     */
    private function shouldShowPrompt($userId, $context)
    {
        // Vérifier la fréquence d'affichage
        $cacheKey = "upgrade_prompt_shown_{$userId}_{$context}";
        $lastShown = Cache::get($cacheKey);

        if ($lastShown) {
            $hoursSinceLastShown = Carbon::parse($lastShown)->diffInHours(now());
            
            // Règles de fréquence selon le contexte
            $minHours = match($context) {
                'download_limit_reached' => 1,  // Peut être affiché souvent
                'premium_content_blocked' => 2, // Modéré
                'dashboard' => 24,              // Une fois par jour max
                'profile' => 48,                // Tous les 2 jours max
                default => 12,                  // Par défaut 12h
            };

            if ($hoursSinceLastShown < $minHours) {
                return false;
            }
        }

        // Vérifier si l'utilisateur a récemment fermé des prompts
        $dismissedKey = "upgrade_prompt_dismissed_{$userId}";
        $dismissedUntil = Cache::get($dismissedKey);

        if ($dismissedUntil && Carbon::parse($dismissedUntil)->isFuture()) {
            return false;
        }

        return true;
    }

    /**
     * Enregistre l'affichage d'un prompt d'upgrade
     */
    public function trackUpgradePrompt($userId, $context, $promptData = [])
    {
        // Marquer comme affiché dans le cache
        $cacheKey = "upgrade_prompt_shown_{$userId}_{$context}";
        Cache::put($cacheKey, now(), 86400); // 24h

        // Enregistrer dans les analytics
        $this->analyticsService->track($userId, 'upgrade_prompt_shown', [
            'context' => $context,
            'prompt_data' => $promptData,
            'user_subscription' => User::find($userId)->subscription_type,
        ]);
    }

    /**
     * Enregistre le clic sur un prompt d'upgrade
     */
    public function trackUpgradeClick($userId, $context, $action = 'primary_cta')
    {
        $this->analyticsService->track($userId, 'upgrade_prompt_clicked', [
            'context' => $context,
            'action' => $action,
            'user_subscription' => User::find($userId)->subscription_type,
        ]);
    }

    /**
     * Enregistre la fermeture d'un prompt
     */
    public function trackPromptDismissed($userId, $context, $dismissDuration = 24)
    {
        // Marquer comme fermé temporairement
        $dismissedKey = "upgrade_prompt_dismissed_{$userId}";
        Cache::put($dismissedKey, now()->addHours($dismissDuration), $dismissDuration * 3600);

        $this->analyticsService->track($userId, 'upgrade_prompt_dismissed', [
            'context' => $context,
            'dismiss_duration' => $dismissDuration,
        ]);
    }

    /**
     * Récupère le taux de conversion des prompts
     */
    public function getConversionRate($period = 30)
    {
        $startDate = now()->subDays($period);

        // Prompts affichés
        $promptsShown = Analytics::where('event_type', 'upgrade_prompt_shown')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Clics sur les prompts
        $promptsClicked = Analytics::where('event_type', 'upgrade_prompt_clicked')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Conversions (upgrades) après un prompt
        $conversions = Analytics::where('event_type', 'subscription_upgraded')
            ->where('created_at', '>=', $startDate)
            ->whereExists(function ($query) use ($startDate) {
                $query->select(\DB::raw(1))
                      ->from('analytics as a2')
                      ->whereRaw('a2.user_id = analytics.user_id')
                      ->where('a2.event_type', 'upgrade_prompt_shown')
                      ->where('a2.created_at', '>=', $startDate)
                      ->where('a2.created_at', '<', \DB::raw('analytics.created_at'));
            })
            ->count();

        return [
            'period_days' => $period,
            'prompts_shown' => $promptsShown,
            'prompts_clicked' => $promptsClicked,
            'conversions' => $conversions,
            'click_rate' => $promptsShown > 0 ? round(($promptsClicked / $promptsShown) * 100, 2) : 0,
            'conversion_rate' => $promptsShown > 0 ? round(($conversions / $promptsShown) * 100, 2) : 0,
            'click_to_conversion' => $promptsClicked > 0 ? round(($conversions / $promptsClicked) * 100, 2) : 0,
        ];
    }

    /**
     * Récupère les statistiques par contexte
     */
    public function getStatsByContext($period = 30)
    {
        $startDate = now()->subDays($period);

        $contexts = Analytics::where('event_type', 'upgrade_prompt_shown')
            ->where('created_at', '>=', $startDate)
            ->select('event_data->context as context')
            ->groupBy('context')
            ->pluck('context');

        $stats = [];

        foreach ($contexts as $context) {
            $shown = Analytics::where('event_type', 'upgrade_prompt_shown')
                ->where('created_at', '>=', $startDate)
                ->whereJsonContains('event_data->context', $context)
                ->count();

            $clicked = Analytics::where('event_type', 'upgrade_prompt_clicked')
                ->where('created_at', '>=', $startDate)
                ->whereJsonContains('event_data->context', $context)
                ->count();

            $stats[$context] = [
                'shown' => $shown,
                'clicked' => $clicked,
                'click_rate' => $shown > 0 ? round(($clicked / $shown) * 100, 2) : 0,
            ];
        }

        return $stats;
    }

    /**
     * Récupère le nombre de vues de tutoriels d'un utilisateur
     */
    private function getUserTutorialViews($userId)
    {
        return Analytics::where('user_id', $userId)
            ->where('event_type', 'tutorial_viewed')
            ->count();
    }

    /**
     * Récupère une réduction active
     */
    private function getActiveDiscount($plan)
    {
        // Logique pour récupérer les réductions actives
        // Pour l'exemple, retourner une réduction fictive
        return [
            'percentage' => 20,
            'code' => 'PREMIUM20',
            'expires_at' => now()->addDays(7),
        ];
    }

    /**
     * Récupère le nombre de tutoriels premium cachés
     */
    private function getHiddenPremiumCount()
    {
        return \App\Models\Tutorial::where('subscription_type', 'premium')
            ->where('status', 'published')
            ->count();
    }
}
