<?php

namespace App\Listeners;

use App\Events\DownloadCompleted;
use App\Services\AnalyticsService;
use App\Services\RestrictionService;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TrackDownloadAndCheckLimits implements ShouldQueue
{
    use InteractsWithQueue;

    protected $analyticsService;
    protected $restrictionService;
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(
        AnalyticsService $analyticsService,
        RestrictionService $restrictionService,
        NotificationService $notificationService
    ) {
        $this->analyticsService = $analyticsService;
        $this->restrictionService = $restrictionService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(DownloadCompleted $event): void
    {
        $user = $event->user;
        $tutorial = $event->tutorial;
        $download = $event->download;
        $fileName = $event->fileName;

        try {
            // Enregistrer l'analytics du téléchargement
            $this->analyticsService->trackDownload($user->id, $tutorial->id, $fileName);
            
            Log::info('Download tracked in analytics', [
                'user_id' => $user->id,
                'tutorial_id' => $tutorial->id,
                'file_name' => $fileName,
                'download_id' => $download->id
            ]);

            // Invalider le cache d'utilisation pour avoir les données à jour
            $this->restrictionService->clearUsageCache($user->id);

            // Vérifier les limites et générer des prompts d'upgrade si nécessaire
            $shouldShowUpgrade = $this->restrictionService->shouldShowUpgradePrompt($user->id);
            
            if ($shouldShowUpgrade) {
                Log::info('User approaching download limits, sending upgrade prompt', [
                    'user_id' => $user->id,
                    'subscription_type' => $user->subscription_type
                ]);

                // Envoyer un rappel d'upgrade
                $this->notificationService->sendUpgradeReminder($user);
            }

            // Récupérer les téléchargements restants pour logging
            $remainingDownloads = $this->restrictionService->getRemainingDownloads($user->id);
            
            Log::info('Download limits status after download', [
                'user_id' => $user->id,
                'subscription_type' => $user->subscription_type,
                'daily_remaining' => $remainingDownloads['daily']['remaining'] ?? 'unlimited',
                'monthly_remaining' => $remainingDownloads['monthly']['remaining'] ?? 'unlimited'
            ]);

            // Si l'utilisateur free a atteint ses limites, envoyer une notification spéciale
            if ($user->subscription_type === 'free') {
                $canDownload = $this->restrictionService->canDownload($user->id);
                
                if (!$canDownload['can_download']) {
                    Log::info('Free user reached download limit', [
                        'user_id' => $user->id,
                        'limit_type' => $canDownload['limit_type'] ?? 'unknown',
                        'reason' => $canDownload['reason'] ?? 'Limit reached'
                    ]);

                    // Envoyer une notification d'upgrade immédiate
                    $this->notificationService->sendUpgradeReminder($user);
                }
            }

            // Mettre à jour les statistiques d'utilisation globales
            $this->updateGlobalUsageStats($user, $download);

        } catch (\Exception $e) {
            Log::error('Error handling download completion event', [
                'user_id' => $user->id,
                'tutorial_id' => $tutorial->id,
                'download_id' => $download->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lancer l'exception pour que le job soit marqué comme échoué
            throw $e;
        }
    }

    /**
     * Met à jour les statistiques d'utilisation globales
     */
    private function updateGlobalUsageStats($user, $download): void
    {
        try {
            // Log des statistiques pour monitoring
            $stats = $this->restrictionService->getUsageStatistics();
            
            Log::info('Global usage statistics updated', [
                'total_downloads_today' => $stats['downloads']['today'] ?? 0,
                'total_downloads_this_week' => $stats['downloads']['this_week'] ?? 0,
                'total_downloads_this_month' => $stats['downloads']['this_month'] ?? 0,
                'users_at_daily_limit' => $stats['users_at_limits']['daily'] ?? 0,
                'users_at_monthly_limit' => $stats['users_at_limits']['monthly'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to update global usage statistics', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(DownloadCompleted $event, \Throwable $exception): void
    {
        Log::error('Failed to process download completion event', [
            'user_id' => $event->user->id,
            'tutorial_id' => $event->tutorial->id,
            'download_id' => $event->download->id,
            'file_name' => $event->fileName,
            'error' => $exception->getMessage()
        ]);
    }
}
