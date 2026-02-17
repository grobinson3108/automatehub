<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\UserAppCredential;
use App\Models\WatchtrendUserSetting;
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
            'summary_language' => 'required|in:fr,en',
            'items_per_page'   => 'required|integer|min:5|max:100',
        ]);

        WatchtrendUserSetting::where('user_id', Auth::id())
            ->update($validated);

        return response()->json(['success' => true, 'message' => 'Preferences updated successfully.']);
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
