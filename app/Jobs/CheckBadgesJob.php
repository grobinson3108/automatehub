<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckBadgesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?User $user;
    protected bool $checkAllUsers;

    /**
     * Nombre de tentatives maximum
     */
    public int $tries = 2;

    /**
     * Délai avant retry en secondes
     */
    public int $backoff = 30;

    /**
     * Timeout du job en secondes
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(?User $user = null, bool $checkAllUsers = false)
    {
        $this->user = $user;
        $this->checkAllUsers = $checkAllUsers;
        $this->onQueue('badges');
    }

    /**
     * Execute the job.
     */
    public function handle(BadgeService $badgeService): void
    {
        try {
            if ($this->checkAllUsers) {
                $this->checkAllUsersBadges($badgeService);
            } elseif ($this->user) {
                $this->checkUserBadges($badgeService, $this->user);
            } else {
                Log::warning('CheckBadgesJob called without user or checkAllUsers flag');
                return;
            }

        } catch (\Exception $e) {
            Log::error('CheckBadgesJob failed', [
                'user_id' => $this->user?->id,
                'check_all_users' => $this->checkAllUsers,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    /**
     * Vérifie les badges pour un utilisateur spécifique
     */
    private function checkUserBadges(BadgeService $badgeService, User $user): void
    {
        Log::info('Checking badges for user', ['user_id' => $user->id]);

        $newBadges = $badgeService->checkAndAwardBadges($user->id);

        if (!empty($newBadges)) {
            Log::info('New badges awarded', [
                'user_id' => $user->id,
                'badges_count' => count($newBadges),
                'badges' => array_map(fn($badge) => $badge->name, $newBadges)
            ]);

            // Envoyer les notifications pour chaque nouveau badge
            $notificationService = app(\App\Services\NotificationService::class);
            foreach ($newBadges as $badge) {
                $notificationService->sendBadgeEarnedNotification($user, $badge);
            }
        } else {
            Log::debug('No new badges for user', ['user_id' => $user->id]);
        }
    }

    /**
     * Vérifie les badges pour tous les utilisateurs
     */
    private function checkAllUsersBadges(BadgeService $badgeService): void
    {
        Log::info('Starting badge check for all users');

        $totalUsers = 0;
        $totalNewBadges = 0;
        $batchSize = 100;

        User::chunk($batchSize, function ($users) use ($badgeService, &$totalUsers, &$totalNewBadges) {
            foreach ($users as $user) {
                try {
                    $newBadges = $badgeService->checkAndAwardBadges($user->id);
                    $totalUsers++;
                    $totalNewBadges += count($newBadges);

                    // Envoyer les notifications pour les nouveaux badges
                    if (!empty($newBadges)) {
                        $notificationService = app(\App\Services\NotificationService::class);
                        foreach ($newBadges as $badge) {
                            $notificationService->sendBadgeEarnedNotification($user, $badge);
                        }
                    }

                } catch (\Exception $e) {
                    Log::error('Failed to check badges for user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue avec les autres utilisateurs
                }
            }

            // Log du progrès tous les 100 utilisateurs
            Log::info('Badge check progress', [
                'users_processed' => $totalUsers,
                'total_new_badges' => $totalNewBadges
            ]);
        });

        Log::info('Badge check completed for all users', [
            'total_users_processed' => $totalUsers,
            'total_new_badges_awarded' => $totalNewBadges
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CheckBadgesJob failed permanently', [
            'user_id' => $this->user?->id,
            'check_all_users' => $this->checkAllUsers,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        $tags = ['badges'];

        if ($this->checkAllUsers) {
            $tags[] = 'all-users';
        } elseif ($this->user) {
            $tags[] = 'user:' . $this->user->id;
        }

        return $tags;
    }
}
