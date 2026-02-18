<?php

namespace App\Jobs\WatchTrend;

use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendUserSetting;
use App\Models\WatchtrendWatch;
use App\Services\WatchTrend\WebhookNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWebhookNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(public readonly WatchtrendWatch $watch) {}

    public function handle(WebhookNotificationService $webhookService): void
    {
        $settings = WatchtrendUserSetting::where('user_id', $this->watch->user_id)->first();

        if (!$settings) {
            return;
        }

        $hasSlack   = !empty($settings->slack_webhook_url);
        $hasDiscord = !empty($settings->discord_webhook_url);

        if (!$hasSlack && !$hasDiscord) {
            Log::info("WatchTrend Webhook: no webhook URLs for user {$this->watch->user_id}");
            return;
        }

        $analyses = WatchtrendAnalysis::where('watch_id', $this->watch->id)
            ->whereHas('collectedItem', fn($q) => $q->where('is_read', false))
            ->where('relevance_score', '>=', 50)
            ->orderByDesc('relevance_score')
            ->limit(5)
            ->with('collectedItem')
            ->get();

        if ($analyses->isEmpty()) {
            return;
        }

        $slackSent   = 0;
        $discordSent = 0;

        foreach ($analyses as $analysis) {
            if ($hasSlack) {
                $ok = $webhookService->sendSlackNotification($analysis, $settings->slack_webhook_url);
                if ($ok) {
                    $slackSent++;
                }
            }

            if ($hasDiscord) {
                $ok = $webhookService->sendDiscordNotification($analysis, $settings->discord_webhook_url);
                if ($ok) {
                    $discordSent++;
                }
            }
        }

        Log::info("WatchTrend Webhook: {$slackSent} Slack + {$discordSent} Discord notifications sent for watch {$this->watch->id}");
    }
}
