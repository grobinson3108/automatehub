<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendUserSetting;
use App\Services\WatchTrend\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService) {}

    /**
     * Public Telegram webhook endpoint.
     * Receives updates from Telegram and dispatches to appropriate handlers.
     */
    public function webhook(Request $request): Response
    {
        $update = $request->all();

        Log::info('WatchTrend Telegram webhook received', ['update_id' => $update['update_id'] ?? null]);

        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $chatId        = (string) ($callbackQuery['message']['chat']['id'] ?? '');
            $callbackData  = $callbackQuery['data'] ?? '';
            $callbackId    = $callbackQuery['id'] ?? '';

            $settings = WatchtrendUserSetting::where('telegram_chat_id', $chatId)->first();

            if ($settings && $callbackData && $callbackId) {
                $this->telegramService->handleCallback(
                    $callbackData,
                    $chatId,
                    $settings->telegram_bot_token,
                    $callbackId
                );
            }
        } elseif (isset($update['message'])) {
            $message = $update['message'];
            $chatId  = (string) ($message['chat']['id'] ?? '');
            $text    = $message['text'] ?? '';

            $settings = WatchtrendUserSetting::where('telegram_chat_id', $chatId)->first();

            if ($settings && str_starts_with($text, '/')) {
                $this->telegramService->handleCommand(
                    $text,
                    $chatId,
                    $settings->telegram_bot_token,
                    $settings->user_id
                );
            }
        }

        return response('OK', 200);
    }

    /**
     * Save Telegram bot token and chat ID, then register the webhook with Telegram.
     */
    public function setup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'telegram_bot_token' => 'required|string|min:20',
            'telegram_chat_id'   => 'required|string',
        ]);

        $settings = WatchtrendUserSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            ['ai_mode' => 'standard', 'telegram_paused' => false, 'onboarding_completed' => false]
        );

        $settings->update([
            'telegram_bot_token' => $validated['telegram_bot_token'],
            'telegram_chat_id'   => $validated['telegram_chat_id'],
            'telegram_paused'    => false,
        ]);

        $webhookUrl = route('watchtrend.telegram.webhook');

        $response = Http::post(
            "https://api.telegram.org/bot{$validated['telegram_bot_token']}/setWebhook",
            ['url' => $webhookUrl]
        );

        if (!$response->successful() || !($response->json('ok'))) {
            Log::warning('WatchTrend Telegram: setWebhook failed', [
                'user_id' => Auth::id(),
                'body'    => mb_substr($response->body(), 0, 500),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'enregistrer le webhook Telegram. Vérifiez votre token.',
            ], 422);
        }

        Log::info('WatchTrend Telegram: webhook set', ['user_id' => Auth::id(), 'webhook_url' => $webhookUrl]);

        return response()->json([
            'success' => true,
            'message' => 'Telegram configuré avec succès.',
        ]);
    }

    /**
     * Send a test message to verify the Telegram configuration.
     */
    public function test(Request $request): JsonResponse
    {
        $settings = WatchtrendUserSetting::where('user_id', Auth::id())->first();

        if (!$settings || !$settings->telegram_bot_token || !$settings->telegram_chat_id) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram non configuré. Enregistrez d\'abord votre token et votre Chat ID.',
            ], 422);
        }

        $sent = $this->telegramService->sendMessage(
            $settings->telegram_chat_id,
            $settings->telegram_bot_token,
            '✅ <b>WatchTrend connecté !</b> Vous recevrez vos suggestions de veille ici.'
        );

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'envoyer le message de test. Vérifiez votre configuration.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message de test envoyé avec succès.',
        ]);
    }
}
