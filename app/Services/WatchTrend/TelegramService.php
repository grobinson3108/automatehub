<?php

namespace App\Services\WatchTrend;

use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendFeedback;
use App\Models\WatchtrendUserSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private const CATEGORY_EMOJIS = [
        'critical_update' => '🚨',
        'trend'           => '📈',
        'worth_watching'  => '👀',
        'low_relevance'   => '📌',
    ];

    /**
     * Send a text message via Telegram Bot API.
     *
     * @param string $chatId
     * @param string $token
     * @param string $text
     * @param array  $inlineKeyboard Array of button rows [[['text' => '...', 'callback_data' => '...']]]
     */
    public function sendMessage(string $chatId, string $token, string $text, array $inlineKeyboard = []): bool
    {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];

        if (!empty($inlineKeyboard)) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard,
            ]);
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

        if (!$response->successful()) {
            Log::warning('WatchTrend Telegram: sendMessage failed', [
                'chat_id' => $chatId,
                'status'  => $response->status(),
                'body'    => mb_substr($response->body(), 0, 500),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Format and send a suggestion (analysis) as a Telegram message with inline buttons.
     */
    public function sendSuggestion(WatchtrendAnalysis $analysis, string $chatId, string $token): bool
    {
        $item     = $analysis->collectedItem;
        $emoji    = self::CATEGORY_EMOJIS[$analysis->category] ?? '📌';
        $summary  = mb_substr($analysis->summary_fr ?? '', 0, 200);
        $title    = $item?->title ?? 'Sans titre';
        $url      = $item?->url ?? '';
        $score    = $analysis->relevance_score;

        $text  = "{$emoji} <b>" . htmlspecialchars($title) . "</b>\n\n";
        $text .= htmlspecialchars($summary) . "\n\n";
        $text .= "Score : <b>{$score}/100</b>";

        if ($url) {
            $text .= "\n\n<a href=\"" . htmlspecialchars($url) . "\">Lire l'article</a>";
        }

        $inlineKeyboard = [
            [
                ['text' => 'Pertinent ✅',     'callback_data' => "relevant:{$analysis->id}"],
                ['text' => 'Non pertinent ❌',  'callback_data' => "irrelevant:{$analysis->id}"],
                ['text' => 'Plus tard ⏰',      'callback_data' => "later:{$analysis->id}"],
            ],
        ];

        return $this->sendMessage($chatId, $token, $text, $inlineKeyboard);
    }

    /**
     * Handle a Telegram callback_query.
     * callback_data format: "action:analysis_id" (e.g. "relevant:123")
     *
     * @param string $callbackData
     * @param string $chatId
     * @param string $token
     * @param string $callbackQueryId Required to answer the callback
     */
    public function handleCallback(string $callbackData, string $chatId, string $token, string $callbackQueryId): void
    {
        $parts      = explode(':', $callbackData, 2);
        $action     = $parts[0] ?? '';
        $analysisId = (int) ($parts[1] ?? 0);

        if (!$analysisId) {
            $this->answerCallback($token, $callbackQueryId, 'Action invalide.');
            return;
        }

        $analysis = WatchtrendAnalysis::find($analysisId);

        if (!$analysis) {
            $this->answerCallback($token, $callbackQueryId, 'Analyse introuvable.');
            return;
        }

        switch ($action) {
            case 'relevant':
                WatchtrendFeedback::updateOrCreate(
                    ['watch_id' => $analysis->watch_id, 'analysis_id' => $analysis->id],
                    ['rating' => 5, 'source_channel' => 'telegram']
                );
                $this->answerCallback($token, $callbackQueryId, 'Marqué comme pertinent !');
                break;

            case 'irrelevant':
                WatchtrendFeedback::updateOrCreate(
                    ['watch_id' => $analysis->watch_id, 'analysis_id' => $analysis->id],
                    ['rating' => 1, 'source_channel' => 'telegram']
                );
                $this->answerCallback($token, $callbackQueryId, 'Marqué comme non pertinent.');
                break;

            case 'later':
                $this->answerCallback($token, $callbackQueryId, 'Rappelé plus tard.');
                break;

            default:
                $this->answerCallback($token, $callbackQueryId, 'Action inconnue.');
                break;
        }
    }

    /**
     * Handle a Telegram bot command.
     *
     * @param string $command   e.g. '/digest', '/stats', '/pause', '/resume'
     * @param string $chatId
     * @param string $token
     * @param int    $userId    AutomateHub user ID
     */
    public function handleCommand(string $command, string $chatId, string $token, int $userId): void
    {
        $settings = WatchtrendUserSetting::where('user_id', $userId)->first();

        if (!$settings) {
            $this->sendMessage($chatId, $token, "Aucun compte WatchTrend associé à ce chat.");
            return;
        }

        $baseCommand = strtok($command, ' ');

        switch ($baseCommand) {
            case '/digest':
                $this->sendMessage($chatId, $token, "Votre digest est en cours de préparation. Vous le recevrez par email sous peu.");
                break;

            case '/stats':
                $watchIds      = \App\Models\WatchtrendWatch::where('user_id', $userId)->pluck('id');
                $watchCount    = $watchIds->count();
                $analysisCount = \App\Models\WatchtrendAnalysis::whereIn('watch_id', $watchIds)->count();
                $avgScore      = \App\Models\WatchtrendAnalysis::whereIn('watch_id', $watchIds)->avg('relevance_score') ?? 0;

                $text = "📊 <b>Vos statistiques WatchTrend</b>\n\n";
                $text .= "Watches actives : <b>{$watchCount}</b>\n";
                $text .= "Analyses totales : <b>{$analysisCount}</b>\n";
                $text .= "Score moyen : <b>" . round($avgScore, 1) . "/100</b>";

                $this->sendMessage($chatId, $token, $text);
                break;

            case '/pause':
                $settings->update(['telegram_paused' => true]);
                $this->sendMessage($chatId, $token, "Notifications Telegram <b>suspendues</b>. Envoyez /resume pour les réactiver.");
                break;

            case '/resume':
                $settings->update(['telegram_paused' => false]);
                $this->sendMessage($chatId, $token, "Notifications Telegram <b>réactivées</b>. Vous recevrez à nouveau vos suggestions.");
                break;

            default:
                $this->sendMessage($chatId, $token, "Commande inconnue. Commandes disponibles : /digest, /stats, /pause, /resume");
                break;
        }
    }

    /**
     * Answer a Telegram callback query (required to remove the loading spinner).
     */
    private function answerCallback(string $token, string $callbackQueryId, string $text = ''): void
    {
        Http::post("https://api.telegram.org/bot{$token}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text'              => $text,
        ]);
    }
}
