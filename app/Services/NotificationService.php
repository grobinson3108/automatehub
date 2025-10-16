<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tutorial;
use App\Models\Badge;
use App\Models\UserTutorialProgress;
use App\Models\Download;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Types de notifications disponibles
     */
    const NOTIFICATION_TYPES = [
        'welcome_email' => 'Email de bienvenue',
        'upgrade_reminder' => 'Rappel d\'upgrade',
        'new_tutorial' => 'Nouveau tutoriel',
        'badge_earned' => 'Badge obtenu',
        'activity_reminder' => 'Rappel d\'activité',
        'trial_ending' => 'Fin d\'essai',
        'weekly_digest' => 'Digest hebdomadaire',
        'marketing' => 'Emails marketing',
    ];

    /**
     * Vérifie les préférences de notification d'un utilisateur
     */
    public function canSendNotification(User $user, string $type): bool
    {
        // Vérifier si l'utilisateur a des préférences définies
        $preferences = $this->getUserNotificationPreferences($user->id);
        
        // Si pas de préférences, autoriser par défaut (sauf marketing)
        if (empty($preferences)) {
            return $type !== 'marketing';
        }

        return $preferences[$type] ?? false;
    }

    /**
     * Récupère les préférences de notification d'un utilisateur
     */
    public function getUserNotificationPreferences(int $userId): array
    {
        $cacheKey = "notification_preferences_{$userId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            $user = User::find($userId);
            
            // Si pas de préférences sauvegardées, retourner les valeurs par défaut
            if (!$user || !$user->notification_preferences) {
                return $this->getDefaultNotificationPreferences();
            }

            return json_decode($user->notification_preferences, true) ?: $this->getDefaultNotificationPreferences();
        });
    }

    /**
     * Met à jour les préférences de notification d'un utilisateur
     */
    public function updateNotificationPreferences(int $userId, array $preferences): bool
    {
        try {
            $user = User::findOrFail($userId);
            
            // Valider les préférences
            $validatedPreferences = $this->validateNotificationPreferences($preferences);
            
            $user->update([
                'notification_preferences' => json_encode($validatedPreferences)
            ]);

            // Invalider le cache
            Cache::forget("notification_preferences_{$userId}");

            Log::info('Notification preferences updated', [
                'user_id' => $userId,
                'preferences' => $validatedPreferences
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update notification preferences', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie l'email de bienvenue à un nouvel utilisateur
     */
    public function sendWelcomeEmail(User $user)
    {
        if (!$this->canSendNotification($user, 'welcome_email')) {
            return false;
        }

        try {
            $data = [
                'user' => $user,
                'login_url' => route('login'),
                'tutorials_url' => route('user.tutorials.index'),
                'support_email' => config('mail.support_email', 'support@automatehub.fr'),
                'tracking_pixel' => $this->generateTrackingPixel($user->id, 'welcome_email')
            ];

            // Personnaliser selon le type d'utilisateur
            if ($user->is_professional) {
                $data['template'] = 'professional_welcome';
                $data['business_features'] = [
                    'Tutoriels orientés entreprise',
                    'Support prioritaire',
                    'Facturation adaptée aux entreprises'
                ];
            } else {
                $data['template'] = 'individual_welcome';
                $data['getting_started'] = [
                    'Complétez votre profil',
                    'Explorez les tutoriels gratuits',
                    'Rejoignez notre communauté'
                ];
            }

            // Ajouter des recommandations basées sur le niveau n8n
            if ($user->n8n_level) {
                $data['recommended_tutorials'] = $this->getRecommendedTutorialsForLevel($user->n8n_level);
            }

            $this->queueEmail($user, 'emails.welcome', $data, 'Bienvenue sur AutomateHub ! 🚀', 'welcome_email');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie un rappel d'upgrade à un utilisateur
     */
    public function sendUpgradeReminder(User $user)
    {
        if (!$this->canSendNotification($user, 'upgrade_reminder') || $user->subscription_type === 'pro') {
            return false;
        }

        try {
            $restrictionService = app(RestrictionService::class);
            $usage = $restrictionService->getRemainingDownloads($user->id);
            $prompts = $restrictionService->upgradePrompts($user->id);

            $data = [
                'user' => $user,
                'current_plan' => $user->subscription_type,
                'usage_stats' => $usage,
                'upgrade_prompts' => $prompts,
                'upgrade_url' => route('user.subscription.upgrade'),
                'benefits' => $this->getUpgradeBenefits($user->subscription_type),
                'tracking_pixel' => $this->generateTrackingPixel($user->id, 'upgrade_reminder')
            ];

            // Personnaliser le message selon l'utilisation
            if ($user->subscription_type === 'free') {
                $subject = 'Débloquez plus de téléchargements avec Premium 🔓';
                $data['template'] = 'upgrade_to_premium';
            } else {
                $subject = 'Passez au niveau supérieur avec Pro 🚀';
                $data['template'] = 'upgrade_to_pro';
            }

            $this->queueEmail($user, 'emails.upgrade_reminder', $data, $subject, 'upgrade_reminder');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send upgrade reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie une notification de nouveau tutoriel
     */
    public function sendNewTutorialNotification(User $user, Tutorial $tutorial)
    {
        if (!$this->canSendNotification($user, 'new_tutorial')) {
            return false;
        }

        try {
            // Vérifier si l'utilisateur peut accéder à ce tutoriel
            $tutorialService = app(TutorialService::class);
            if (!$tutorialService->canAccessTutorial($user->id, $tutorial->id)) {
                // Si l'utilisateur ne peut pas accéder, envoyer une notification d'upgrade
                return $this->sendUpgradeReminder($user);
            }

            $data = [
                'user' => $user,
                'tutorial' => $tutorial,
                'tutorial_url' => route('user.tutorials.show', $tutorial->id),
                'category' => $tutorial->category,
                'estimated_time' => $tutorial->duration_minutes,
                'difficulty' => $tutorial->difficulty,
                'related_tutorials' => $this->getRelatedTutorials($tutorial, 3),
                'tracking_pixel' => $this->generateTrackingPixel($user->id, 'new_tutorial', $tutorial->id)
            ];

            // Personnaliser selon le niveau du tutoriel
            if ($tutorial->level === $user->n8n_level) {
                $data['relevance_message'] = 'Ce tutoriel correspond parfaitement à votre niveau !';
            } elseif ($tutorial->level === 'beginner' && $user->n8n_level !== 'beginner') {
                $data['relevance_message'] = 'Un excellent rappel des fondamentaux.';
            } else {
                $data['relevance_message'] = 'Prêt à relever un nouveau défi ?';
            }

            $subject = 'Nouveau tutoriel : ' . $tutorial->title . ' 📚';
            $this->queueEmail($user, 'emails.new_tutorial', $data, $subject, 'new_tutorial');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send new tutorial notification', [
                'user_id' => $user->id,
                'tutorial_id' => $tutorial->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie une notification de badge obtenu
     */
    public function sendBadgeEarnedNotification(User $user, Badge $badge)
    {
        if (!$this->canSendNotification($user, 'badge_earned')) {
            return false;
        }

        try {
            $badgeService = app(BadgeService::class);
            $userBadges = $badgeService->getAvailableBadges($user->id);
            $progress = $badgeService->calculateN8nProgress($user->id);

            $data = [
                'user' => $user,
                'badge' => $badge,
                'total_badges' => count($userBadges['earned']),
                'next_badges' => array_slice($userBadges['available'], 0, 3),
                'n8n_progress' => $progress,
                'badges_url' => route('user.badges.index'),
                'share_url' => route('user.badges.share', $badge->id),
                'tracking_pixel' => $this->generateTrackingPixel($user->id, 'badge_earned', $badge->id)
            ];

            // Messages de félicitations selon le type de badge
            switch ($badge->type) {
                case 'registration':
                    $data['congratulation_message'] = 'Félicitations pour votre inscription ! 🎉';
                    break;
                case 'tutorial_completion':
                    $data['congratulation_message'] = 'Bravo pour votre progression ! 📈';
                    break;
                case 'download':
                    $data['congratulation_message'] = 'Vous êtes un utilisateur actif ! 💪';
                    break;
                case 'n8n_level':
                    $data['congratulation_message'] = 'Votre expertise n8n grandit ! 🚀';
                    break;
                case 'streak':
                    $data['congratulation_message'] = 'Quelle régularité impressionnante ! 🔥';
                    break;
                default:
                    $data['congratulation_message'] = 'Un nouveau badge débloqué ! ⭐';
            }

            $subject = '🏆 Badge débloqué : ' . $badge->name;
            $this->queueEmail($user, 'emails.badge_earned', $data, $subject, 'badge_earned');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send badge earned notification', [
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie une notification de rappel d'activité
     */
    public function sendActivityReminder(User $user)
    {
        if (!$this->canSendNotification($user, 'activity_reminder')) {
            return false;
        }

        try {
            $analyticsService = app(AnalyticsService::class);
            $userStats = $analyticsService->getUserStats($user->id);
            $tutorialService = app(TutorialService::class);
            $recommendations = $tutorialService->getRecommendations($user->id, 5);

            $data = [
                'user' => $user,
                'last_activity' => $user->last_activity_at,
                'stats' => $userStats,
                'recommendations' => $recommendations,
                'login_url' => route('login'),
                'unsubscribe_url' => route('user.notifications.unsubscribe', $user->id),
                'tracking_pixel' => $this->generateTrackingPixel($user->id, 'activity_reminder')
            ];

            // Personnaliser selon la durée d'inactivité
            $daysSinceLastActivity = $user->last_activity_at ? 
                $user->last_activity_at->diffInDays(now()) : 30;

            if ($daysSinceLastActivity <= 7) {
                $subject = 'De nouveaux tutoriels vous attendent ! 📚';
                $data['message_tone'] = 'friendly';
            } elseif ($daysSinceLastActivity <= 30) {
                $subject = 'Nous vous avez manqué ! Revenez découvrir les nouveautés 🔄';
                $data['message_tone'] = 'encouraging';
            } else {
                $subject = 'Votre apprentissage n8n vous attend ! 🚀';
                $data['message_tone'] = 'motivational';
            }

            $this->queueEmail($user, 'emails.activity_reminder', $data, $subject, 'activity_reminder');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send activity reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie un digest hebdomadaire à l'utilisateur
     */
    public function sendWeeklyDigest(User $user)
    {
        if (!$this->canSendNotification($user, 'weekly_digest')) {
            return false;
        }

        try {
            $analyticsService = app(AnalyticsService::class);
            $tutorialService = app(TutorialService::class);
            $badgeService = app(BadgeService::class);

            $data = [
                'user' => $user,
                'week_stats' => $this->getWeeklyStats($user),
                'new_tutorials' => $this->getNewTutorialsThisWeek($user),
                'progress_summary' => $badgeService->calculateN8nProgress($user->id),
                'achievements' => $this->getWeeklyAchievements($user),
                'recommendations' => $tutorialService->getRecommendations($user->id, 3),
                'community_highlights' => $this->getCommunityHighlights(),
                'tracking_pixel' => $this->generateTrackingPixel($user->id, 'weekly_digest')
            ];

            $subject = 'Votre résumé hebdomadaire AutomateHub 📊';
            $this->queueEmail($user, 'emails.weekly_digest', $data, $subject, 'weekly_digest');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send weekly digest', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Envoie un email en masse à plusieurs utilisateurs
     */
    public function sendBulkEmail(array $userIds, string $template, array $data, string $subject, string $type = 'marketing')
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($userIds as $userId) {
            try {
                $user = User::find($userId);
                if (!$user || !$this->canSendNotification($user, $type)) {
                    continue;
                }

                $personalizedData = array_merge($data, [
                    'user' => $user,
                    'tracking_pixel' => $this->generateTrackingPixel($user->id, $type)
                ]);

                $this->queueEmail($user, $template, $personalizedData, $subject, $type);
                $successCount++;

            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Failed to send bulk email to user', [
                    'user_id' => $userId,
                    'template' => $template,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Bulk email campaign completed', [
            'template' => $template,
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ]);

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ];
    }

    /**
     * Génère un pixel de tracking pour les emails
     */
    private function generateTrackingPixel(int $userId, string $type, $resourceId = null): string
    {
        $trackingId = base64_encode(json_encode([
            'user_id' => $userId,
            'type' => $type,
            'resource_id' => $resourceId,
            'timestamp' => time()
        ]));

        return route('email.tracking.pixel', ['id' => $trackingId]);
    }

    /**
     * Traite l'ouverture d'un email via le pixel de tracking
     */
    public function trackEmailOpen(string $trackingId): void
    {
        try {
            $data = json_decode(base64_decode($trackingId), true);
            
            if (!$data || !isset($data['user_id'], $data['type'])) {
                return;
            }

            $analyticsService = app(AnalyticsService::class);
            $analyticsService->track($data['user_id'], 'email_opened', [
                'email_type' => $data['type'],
                'resource_id' => $data['resource_id'] ?? null,
                'opened_at' => now(),
                'tracking_id' => $trackingId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track email open', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Traite le clic sur un lien dans un email
     */
    public function trackEmailClick(int $userId, string $emailType, string $linkType, $resourceId = null): void
    {
        try {
            $analyticsService = app(AnalyticsService::class);
            $analyticsService->track($userId, 'email_clicked', [
                'email_type' => $emailType,
                'link_type' => $linkType,
                'resource_id' => $resourceId,
                'clicked_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track email click', [
                'user_id' => $userId,
                'email_type' => $emailType,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Met en queue un email pour envoi
     */
    private function queueEmail(User $user, string $template, array $data, string $subject, string $type): void
    {
        Queue::push(function () use ($user, $template, $data, $subject, $type) {
            try {
                Mail::send($template, $data, function ($message) use ($user, $subject) {
                    $message->to($user->email, $user->name)
                           ->subject($subject);
                });

                // Enregistrer l'envoi dans les analytics
                $analyticsService = app(AnalyticsService::class);
                $analyticsService->track($user->id, 'email_sent', [
                    'email_type' => $type,
                    'template' => $template,
                    'sent_at' => now()
                ]);

                Log::info('Email sent successfully', [
                    'user_id' => $user->id,
                    'template' => $template,
                    'type' => $type
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to send email', [
                    'user_id' => $user->id,
                    'template' => $template,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    /**
     * Récupère les préférences par défaut
     */
    private function getDefaultNotificationPreferences(): array
    {
        return [
            'welcome_email' => true,
            'upgrade_reminder' => true,
            'new_tutorial' => true,
            'badge_earned' => true,
            'activity_reminder' => true,
            'trial_ending' => true,
            'weekly_digest' => false,
            'marketing' => false,
        ];
    }

    /**
     * Valide les préférences de notification
     */
    private function validateNotificationPreferences(array $preferences): array
    {
        $validated = [];
        $allowedTypes = array_keys(self::NOTIFICATION_TYPES);

        foreach ($allowedTypes as $type) {
            $validated[$type] = isset($preferences[$type]) ? (bool) $preferences[$type] : false;
        }

        return $validated;
    }

    /**
     * Récupère les statistiques d'emails
     */
    public function getEmailStatistics($period = 30): array
    {
        $startDate = now()->subDays($period);

        $stats = [];
        
        foreach (array_keys(self::NOTIFICATION_TYPES) as $type) {
            $sent = DB::table('analytics')
                ->where('event_type', 'email_sent')
                ->where('created_at', '>=', $startDate)
                ->whereJsonContains('event_data->email_type', $type)
                ->count();

            $opened = DB::table('analytics')
                ->where('event_type', 'email_opened')
                ->where('created_at', '>=', $startDate)
                ->whereJsonContains('event_data->email_type', $type)
                ->count();

            $clicked = DB::table('analytics')
                ->where('event_type', 'email_clicked')
                ->where('created_at', '>=', $startDate)
                ->whereJsonContains('event_data->email_type', $type)
                ->count();

            $stats[$type] = [
                'sent' => $sent,
                'opened' => $opened,
                'clicked' => $clicked,
                'open_rate' => $sent > 0 ? round(($opened / $sent) * 100, 2) : 0,
                'click_rate' => $sent > 0 ? round(($clicked / $sent) * 100, 2) : 0,
                'click_to_open_rate' => $opened > 0 ? round(($clicked / $opened) * 100, 2) : 0,
            ];
        }

        return $stats;
    }

    // Méthodes privées existantes...
    private function getRecommendedTutorialsForLevel($level)
    {
        return Tutorial::where('level', $level)
            ->where('is_published', true)
            ->where('subscription_required', 'free')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['id', 'title', 'description', 'duration_minutes']);
    }

    private function getUpgradeBenefits($currentPlan)
    {
        if ($currentPlan === 'free') {
            return [
                'Téléchargements illimités',
                'Accès aux tutoriels premium',
                'Support prioritaire',
                'Pas de publicité',
                'Accès anticipé aux nouveautés'
            ];
        } else {
            return [
                'Téléchargements vraiment illimités',
                'Tutoriels sur demande personnalisés',
                'Support dédié 24/7',
                'Accès API complet',
                'Consultation individuelle'
            ];
        }
    }

    private function getRelatedTutorials(Tutorial $tutorial, $limit = 3)
    {
        return Tutorial::where('category_id', $tutorial->category_id)
            ->where('id', '!=', $tutorial->id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get(['id', 'title', 'description']);
    }

    private function getWeeklyStats(User $user)
    {
        $startOfWeek = now()->startOfWeek();
        
        return [
            'tutorials_completed' => UserTutorialProgress::where('user_id', $user->id)
                ->where('completed', true)
                ->where('completed_at', '>=', $startOfWeek)
                ->count(),
            'downloads_made' => Download::where('user_id', $user->id)
                ->where('created_at', '>=', $startOfWeek)
                ->count(),
            'time_spent' => $this->calculateTimeSpentThisWeek($user),
            'badges_earned' => $this->getBadgesEarnedThisWeek($user)
        ];
    }

    private function getNewTutorialsThisWeek(User $user)
    {
        $startOfWeek = now()->startOfWeek();
        $tutorialService = app(TutorialService::class);
        
        $newTutorials = Tutorial::where('created_at', '>=', $startOfWeek)
            ->where('is_published', true)
            ->get();

        return $newTutorials->filter(function ($tutorial) use ($user, $tutorialService) {
            return $tutorialService->canAccessTutorial($user->id, $tutorial->id);
        })->take(5);
    }

    private function getWeeklyAchievements(User $user)
    {
        return [];
    }

    private function getCommunityHighlights()
    {
        return [];
    }

    private function calculateTimeSpentThisWeek(User $user)
    {
        return 0;
    }

    private function getBadgesEarnedThisWeek(User $user)
    {
        $startOfWeek = now()->startOfWeek();
        
        return DB::table('user_badges')
            ->join('badges', 'user_badges.badge_id', '=', 'badges.id')
            ->where('user_badges.user_id', $user->id)
            ->where('user_badges.earned_at', '>=', $startOfWeek)
            ->get(['badges.name', 'badges.description', 'user_badges.earned_at']);
    }
}
