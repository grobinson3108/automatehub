<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\NotificationService;
use App\Services\BadgeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailAndAwardBadges implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;
    protected $badgeService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService, BadgeService $badgeService)
    {
        $this->notificationService = $notificationService;
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        try {
            // Envoyer l'email de bienvenue
            $this->notificationService->sendWelcomeEmail($user);
            
            Log::info('Welcome email sent for user registration', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Attribuer les badges de départ
            $awardedBadges = $this->badgeService->checkAndAwardBadges($user->id);
            
            if (!empty($awardedBadges)) {
                Log::info('Initial badges awarded to new user', [
                    'user_id' => $user->id,
                    'badges_count' => count($awardedBadges),
                    'badges' => array_map(function($badge) {
                        return ['id' => $badge->id, 'name' => $badge->name];
                    }, $awardedBadges)
                ]);

                // Envoyer une notification pour chaque badge obtenu
                foreach ($awardedBadges as $badge) {
                    $this->notificationService->sendBadgeEarnedNotification($user, $badge);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error handling user registration event', [
                'user_id' => $user->id,
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
    public function failed(UserRegistered $event, \Throwable $exception): void
    {
        Log::error('Failed to process user registration event', [
            'user_id' => $event->user->id,
            'error' => $exception->getMessage()
        ]);
    }
}
