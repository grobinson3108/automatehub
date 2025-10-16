<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\RestrictionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendUpgradeReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?User $user;
    protected bool $sendToAllEligible;
    protected string $reminderType;

    /**
     * Nombre de tentatives maximum
     */
    public int $tries = 3;

    /**
     * Délai avant retry en secondes
     */
    public int $backoff = 120;

    /**
     * Timeout du job en secondes
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(?User $user = null, bool $sendToAllEligible = false, string $reminderType = 'general')
    {
        $this->user = $user;
        $this->sendToAllEligible = $sendToAllEligible;
        $this->reminderType = $reminderType;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService, RestrictionService $restrictionService): void
    {
        try {
            if ($this->sendToAllEligible) {
                $this->sendToAllEligibleUsers($notificationService, $restrictionService);
            } elseif ($this->user) {
                $this->sendToUser($notificationService, $this->user);
            } else {
                Log::warning('SendUpgradeReminderJob called without user or sendToAllEligible flag');
                return;
            }

        } catch (\Exception $e) {
            Log::error('SendUpgradeReminderJob failed', [
                'user_id' => $this->user?->id,
                'send_to_all' => $this->sendToAllEligible,
                'reminder_type' => $this->reminderType,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    /**
     * Envoie un rappel d'upgrade à un utilisateur spécifique
     */
    private function sendToUser(NotificationService $notificationService, User $user): void
    {
        Log::info('Sending upgrade reminder to user', [
            'user_id' => $user->id,
            'subscription_type' => $user->subscription_type,
            'reminder_type' => $this->reminderType
        ]);

        $result = $notificationService->sendUpgradeReminder($user);

        if ($result) {
            Log::info('Upgrade reminder sent successfully', [
                'user_id' => $user->id,
                'reminder_type' => $this->reminderType
            ]);
        } else {
            Log::info('Upgrade reminder not sent', [
                'user_id' => $user->id,
                'reason' => 'User preferences or subscription level prevented sending'
            ]);
        }
    }

    /**
     * Envoie des rappels d'upgrade à tous les utilisateurs éligibles
     */
    private function sendToAllEligibleUsers(NotificationService $notificationService, RestrictionService $restrictionService): void
    {
        Log::info('Starting upgrade reminder campaign', [
            'reminder_type' => $this->reminderType
        ]);

        $eligibleUsers = $this->getEligibleUsers($restrictionService);
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($eligibleUsers as $user) {
            try {
                $result = $notificationService->sendUpgradeReminder($user);
                
                if ($result) {
                    $sentCount++;
                } else {
                    $skippedCount++;
                }

                // Petit délai pour éviter de surcharger le serveur mail
                usleep(100000); // 0.1 seconde

            } catch (\Exception $e) {
                $skippedCount++;
                Log::error('Failed to send upgrade reminder to user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Upgrade reminder campaign completed', [
            'reminder_type' => $this->reminderType,
            'eligible_users' => count($eligibleUsers),
            'sent_count' => $sentCount,
            'skipped_count' => $skippedCount
        ]);
    }

    /**
     * Récupère les utilisateurs éligibles selon le type de rappel
     */
    private function getEligibleUsers(RestrictionService $restrictionService): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::query();

        switch ($this->reminderType) {
            case 'limit_reached':
                // Utilisateurs qui ont atteint leurs limites de téléchargement
                $query->where('subscription_type', 'free')
                      ->whereHas('downloads', function ($q) {
                          $q->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->havingRaw('COUNT(*) >= 3');
                      }, '>=', 3);
                break;

            case 'inactive_users':
                // Utilisateurs inactifs depuis plus de 7 jours
                $query->where('subscription_type', 'free')
                      ->where('last_activity_at', '<', now()->subDays(7));
                break;

            case 'heavy_users':
                // Utilisateurs très actifs qui pourraient bénéficier d'un upgrade
                $query->where('subscription_type', 'free')
                      ->whereHas('downloads', function ($q) {
                          $q->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->havingRaw('COUNT(*) >= 2');
                      }, '>=', 2)
                      ->whereHas('userTutorialProgress', function ($q) {
                          $q->where('completed', true)
                            ->whereMonth('completed_at', now()->month)
                            ->whereYear('completed_at', now()->year)
                            ->havingRaw('COUNT(*) >= 3');
                      }, '>=', 3);
                break;

            case 'premium_to_pro':
                // Utilisateurs Premium qui pourraient passer Pro
                $query->where('subscription_type', 'premium')
                      ->where('updated_at', '<', now()->subMonths(3)) // Premium depuis 3+ mois
                      ->whereHas('downloads', function ($q) {
                          $q->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->havingRaw('COUNT(*) >= 20'); // Très actifs
                      }, '>=', 20);
                break;

            case 'professional_users':
                // Utilisateurs professionnels en free
                $query->where('subscription_type', 'free')
                      ->where('is_professional', true);
                break;

            default: // 'general'
                // Tous les utilisateurs non-Pro
                $query->whereIn('subscription_type', ['free', 'premium']);
                break;
        }

        // Exclure les utilisateurs qui ont reçu un rappel récemment
        $query->whereDoesntHave('analytics', function ($q) {
            $q->where('event_type', 'email_sent')
              ->whereJsonContains('event_data->email_type', 'upgrade_reminder')
              ->where('created_at', '>=', now()->subDays(7));
        });

        return $query->get();
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendUpgradeReminderJob failed permanently', [
            'user_id' => $this->user?->id,
            'send_to_all' => $this->sendToAllEligible,
            'reminder_type' => $this->reminderType,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        $tags = ['email', 'upgrade-reminder', 'type:' . $this->reminderType];

        if ($this->sendToAllEligible) {
            $tags[] = 'campaign';
        } elseif ($this->user) {
            $tags[] = 'user:' . $this->user->id;
        }

        return $tags;
    }
}
