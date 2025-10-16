<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    /**
     * Nombre de tentatives maximum
     */
    public int $tries = 3;

    /**
     * Délai avant retry en secondes
     */
    public int $backoff = 60;

    /**
     * Timeout du job en secondes
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            Log::info('Processing welcome email job', [
                'user_id' => $this->user->id,
                'email' => $this->user->email
            ]);

            $result = $notificationService->sendWelcomeEmail($this->user);

            if ($result) {
                Log::info('Welcome email job completed successfully', [
                    'user_id' => $this->user->id
                ]);
            } else {
                Log::warning('Welcome email job completed but email was not sent', [
                    'user_id' => $this->user->id,
                    'reason' => 'User preferences or other conditions prevented sending'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Welcome email job failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Re-lancer l'exception pour déclencher le retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Welcome email job failed permanently', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Optionnel : notifier les administrateurs de l'échec
        // ou marquer l'utilisateur pour un suivi manuel
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'email',
            'welcome',
            'user:' . $this->user->id
        ];
    }
}
