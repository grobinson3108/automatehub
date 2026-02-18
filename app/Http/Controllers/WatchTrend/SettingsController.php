<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\UserAppCredential;
use App\Models\WatchtrendUserSetting;
use App\Services\WatchTrend\WebhookNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = WatchtrendUserSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'ai_mode'               => 'standard',
                'telegram_paused'       => false,
                'onboarding_completed'  => false,
                'summary_language'      => 'fr',
                'items_per_page'        => 20,
            ]
        );

        $credential = UserAppCredential::where('user_id', Auth::id())
            ->where('app_slug', 'watchtrend')
            ->where('service', 'openai')
            ->first();

        $hasApiKey    = $credential !== null;
        $apiKeyMasked = null;

        if ($hasApiKey) {
            try {
                $decrypted    = $credential->getDecryptedCredentials();
                $apiKey       = $decrypted['api_key'] ?? '';
                $apiKeyMasked = strlen($apiKey) > 8
                    ? substr($apiKey, 0, 3) . '...' . substr($apiKey, -4)
                    : 'sk-...';
            } catch (\Exception $e) {
                $apiKeyMasked = 'sk-...';
            }
        }

        return view('watchtrend.settings.index', compact('settings', 'hasApiKey', 'apiKeyMasked'));
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'summary_language'    => 'sometimes|in:fr,en',
            'items_per_page'      => 'sometimes|integer|min:5|max:100',
            'slack_webhook_url'   => 'sometimes|nullable|url|max:500',
            'discord_webhook_url' => 'sometimes|nullable|url|max:500',
        ]);

        if (!empty($validated)) {
            WatchtrendUserSetting::where('user_id', Auth::id())
                ->update($validated);
        }

        return response()->json(['success' => true, 'message' => 'Preferences updated successfully.']);
    }

    public function testSlackWebhook(Request $request, WebhookNotificationService $webhookService)
    {
        $request->validate([
            'webhook_url' => 'required|url|max:500',
        ]);

        $payload = [
            'blocks' => [
                [
                    'type' => 'header',
                    'text' => [
                        'type'  => 'plain_text',
                        'text'  => '✅ Test WatchTrend',
                        'emoji' => true,
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'Votre webhook Slack est correctement configuré. Vous recevrez vos notifications WatchTrend ici.',
                    ],
                ],
            ],
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->post($request->webhook_url, $payload);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Message de test envoyé sur Slack !']);
            }

            return response()->json(['success' => false, 'message' => 'Webhook Slack invalide ou inaccessible.'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 422);
        }
    }

    public function testDiscordWebhook(Request $request, WebhookNotificationService $webhookService)
    {
        $request->validate([
            'webhook_url' => 'required|url|max:500',
        ]);

        $payload = [
            'username' => 'WatchTrend',
            'embeds'   => [
                [
                    'title'       => '✅ Test WatchTrend',
                    'description' => 'Votre webhook Discord est correctement configuré. Vous recevrez vos notifications WatchTrend ici.',
                    'color'       => 3066993,
                    'footer'      => ['text' => 'WatchTrend · Test de connexion'],
                ],
            ],
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->post($request->webhook_url, $payload);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Message de test envoyé sur Discord !']);
            }

            return response()->json(['success' => false, 'message' => 'Webhook Discord invalide ou inaccessible.'], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 422);
        }
    }

    public function updateApiKey(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string|min:20',
        ]);

        $credential = UserAppCredential::firstOrNew([
            'user_id'  => Auth::id(),
            'app_slug' => 'watchtrend',
            'service'  => 'openai',
        ]);

        $credential->type      = 'api_key';
        $credential->is_active = true;
        $credential->setCredentials(['api_key' => $validated['api_key']]);
        $credential->save();

        $apiKey = $validated['api_key'];
        $masked = strlen($apiKey) > 8
            ? substr($apiKey, 0, 3) . '...' . substr($apiKey, -4)
            : 'sk-...';

        return response()->json(['success' => true, 'message' => 'API key saved.', 'masked' => $masked]);
    }

    public function deleteApiKey()
    {
        UserAppCredential::where('user_id', Auth::id())
            ->where('app_slug', 'watchtrend')
            ->where('service', 'openai')
            ->delete();

        return response()->json(['success' => true, 'message' => 'API key deleted.']);
    }
}
