<?php

namespace App\Jobs\WatchTrend;

use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendUserSetting;
use App\Models\WatchtrendWatch;
use App\Services\WatchTrend\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(public readonly WatchtrendWatch $watch) {}

    public function handle(TelegramService $telegramService): void
    {
        $settings = WatchtrendUserSetting::where('user_id', $this->watch->user_id)->first();

        if (!$settings) {
            return;
        }

        if ($settings->telegram_paused) {
            Log::info("WatchTrend Telegram: notifications paused for user {$this->watch->user_id}");
            return;
        }

        if (!$settings->telegram_bot_token || !$settings->telegram_chat_id) {
            Log::info("WatchTrend Telegram: no token/chat_id for user {$this->watch->user_id}");
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

        $sent = 0;
        foreach ($analyses as $analysis) {
            $telegramService->sendSuggestion(
                $analysis,
                $settings->telegram_chat_id,
                $settings->telegram_bot_token
            );
            $sent++;
        }

        Log::info("WatchTrend Telegram: {$sent} suggestions sent for watch {$this->watch->id}");
    }
}
