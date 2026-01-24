<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Services\CredentialManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppSettingsController extends Controller
{
    protected $credentialManager;

    public function __construct(CredentialManager $credentialManager)
    {
        $this->credentialManager = $credentialManager;
    }

    /**
     * Display all apps settings
     */
    public function index()
    {
        $user = Auth::user();

        // Get all user's subscribed apps
        $subscribedApps = $user->appSubscriptions()
            ->with('app', 'pricingPlan')
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere(function ($q) {
                          $q->where('status', 'trial')
                            ->where('trial_ends_at', '>', now());
                      });
            })
            ->get();

        // Get credentials status for each app
        $appsWithCredentials = [];
        foreach ($subscribedApps as $subscription) {
            $app = $subscription->app;
            $credentials = $this->credentialManager->getAllForApp($user, $app->slug);

            $appsWithCredentials[] = [
                'app' => $app,
                'subscription' => $subscription,
                'credentials' => $credentials,
                'is_configured' => $user->hasAppCredentials($app->slug),
            ];
        }

        return view('settings.apps.index', [
            'apps' => $appsWithCredentials,
        ]);
    }

    /**
     * Display settings for a specific app
     */
    public function show(App $app)
    {
        $user = Auth::user();

        // Check if user has access to this app
        if (!$user->hasAppAccess($app->slug)) {
            abort(403, 'You do not have access to this app.');
        }

        $subscription = $user->getAppSubscription($app->slug);
        $credentials = $this->credentialManager->getAllForApp($user, $app->slug);

        return view('settings.apps.show', [
            'app' => $app,
            'subscription' => $subscription,
            'credentials' => $credentials,
            'required_integrations' => $app->required_integrations ?? [],
        ]);
    }

    /**
     * Store credentials for an app
     */
    public function storeCredentials(Request $request, App $app)
    {
        $user = Auth::user();

        // Check if user has access to this app
        if (!$user->hasAppAccess($app->slug)) {
            abort(403);
        }

        $validated = $request->validate([
            'service' => 'required|string',
            'type' => 'required|in:api_key,oauth,smtp,webhook',
            'credentials' => 'required|array',
        ]);

        $this->credentialManager->store(
            $user,
            $app->slug,
            $validated['service'],
            $validated['type'],
            $validated['credentials']
        );

        return back()->with('success', 'Credentials saved successfully!');
    }

    /**
     * Delete credentials
     */
    public function deleteCredentials(App $app, string $service)
    {
        $user = Auth::user();

        if (!$user->hasAppAccess($app->slug)) {
            abort(403);
        }

        $this->credentialManager->delete($user, $app->slug, $service);

        return back()->with('success', 'Credentials deleted successfully!');
    }

    /**
     * Verify credentials
     */
    public function verifyCredentials(App $app, string $service)
    {
        $user = Auth::user();

        if (!$user->hasAppAccess($app->slug)) {
            abort(403);
        }

        $verified = $this->credentialManager->verify($user, $app->slug, $service);

        if ($verified) {
            return back()->with('success', 'Credentials verified successfully!');
        }

        return back()->withErrors(['error' => 'Failed to verify credentials.']);
    }
}
