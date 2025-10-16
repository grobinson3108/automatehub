<?php

namespace App\Listeners;

use App\Events\TutorialCompleted;
use App\Services\BadgeService;
use App\Services\AnalyticsService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CheckBadgesAndTrackAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    protected $badgeService;
    protected $analyticsService;
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(
        BadgeService $badgeService,
        AnalyticsService $analyticsService,
        NotificationService $notificationService
    ) {
        $this->badgeService = $badgeService;
        $this->analyticsService = $analyticsService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TutorialCompleted $event): void
    {
        $user = $event->user;
        $tutorial = $event->tutorial;

        try {
            // Enregistrer l'analytics de complétion
            $this->analyticsService->trackTutorialView($user->id, $tutorial->id);
            
            Log::info('Tutorial completion tracked in analytics', [
                'user_id' => $user->id,
                'tutorial_id' => $tutorial->id,
                'tutorial_title' => $tutorial->title
            ]);

            // Vérifier et attribuer les badges
            $awardedBadges = $this->badgeService->checkAndAwardBadges($user->id);
            
            if (!empty($awardedBadges)) {
                Log::info('Badges awarded for tutorial completion', [
                    'user_id' => $user->id,
                    'tutorial_id' => $tutorial->id,
                    'badges_count' => count($awardedBadges),
                    'badges' => array_map(function($badge) {
                        return ['id' => $badge->id, 'name' => $badge->name, 'type' => $badge->type];
                    }, $awardedBadges)
                ]);

                // Envoyer une notification pour chaque nouveau badge
                foreach ($awardedBadges as $badge) {
                    $this->notificationService->sendBadgeEarnedNotification($user, $badge);
                }
            }

            // Calculer la progression n8n mise à jour
            $progress = $this->badgeService->calculateN8nProgress($user->id);
            
            Log::info('User n8n progress updated', [
                'user_id' => $user->id,
                'overall_progress' => $progress['overall']['percentage'] ?? 0,
                'suggested_next_level' => $progress['suggested_next_level'] ?? null
            ]);

            // Si l'utilisateur a atteint un nouveau niveau suggéré, log l'information
            if (isset($progress['suggested_next_level']) && 
                $progress['suggested_next_level'] !== $user->n8n_level) {
                Log::info('User ready for level progression', [
                    'user_id' => $user->id,
                    'current_level' => $user->n8n_level,
                    'suggested_level' => $progress['suggested_next_level']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error handling tutorial completion event', [
                'user_id' => $user->id,
                'tutorial_id' => $tutorial->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lancer l'exception pour que le job soit marqué comme échoué
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(TutorialCompleted $event, \Throwable $exception): void
    {
        Log::error('Failed to process tutorial completion event', [
            'user_id' => $event->user->id,
            'tutorial_id' => $event->tutorial->id,
            'error' => $exception->getMessage()
        ]);
    }
}
