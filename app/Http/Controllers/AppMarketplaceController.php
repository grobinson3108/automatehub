<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppPricingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class AppMarketplaceController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display all active apps grouped by category.
     */
    public function index()
    {
        $apps = App::where('is_active', true)
            ->with('pricingPlans')
            ->orderBy('sort_order')
            ->get();

        $userSubscriptions = collect();
        if (auth()->check()) {
            $userSubscriptions = auth()->user()
                ->appSubscriptions()
                ->with('app')
                ->get()
                ->keyBy(fn($sub) => $sub->app->slug ?? null);
        }

        return view('apps.index', [
            'apps' => $apps,
            'userSubscriptions' => $userSubscriptions,
        ]);
    }

    /**
     * Display a specific app with its pricing plans.
     */
    public function show(App $app)
    {
        $app->load('pricingPlans');

        $userSubscription = null;
        if (auth()->check()) {
            $userSubscription = auth()->user()->getAppSubscription($app->slug);
        }

        return view('apps.show', [
            'app' => $app,
            'userSubscription' => $userSubscription,
        ]);
    }

    /**
     * Display the user's active app subscriptions.
     */
    public function myApps()
    {
        $subscriptions = auth()->user()
            ->appSubscriptions()
            ->with(['app', 'pricingPlan'])
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere(function ($q) {
                        $q->where('status', 'trial')
                            ->where('trial_ends_at', '>', now());
                    });
            })
            ->get();

        return view('apps.my-apps', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Redirect authenticated user to the correct mini-app dashboard.
     */
    public function myAppDashboard(App $app)
    {
        $user = auth()->user();

        if (!$user->hasAppAccess($app->slug)) {
            return redirect()->route('apps.show', $app->slug)
                ->with('error', 'Vous n\'avez pas accès à cette app. Souscrivez un abonnement pour commencer.');
        }

        return match ($app->slug) {
            'videoplan'  => redirect()->route('videoplan.projects.index'),
            'watchtrend' => redirect()->route('watchtrend.dashboard'),
            'orderflow'  => redirect()->route('orderflow.dashboard'),
            default      => redirect()->route('my-apps.index')
                ->with('info', 'App en cours de développement. Revenez bientôt !'),
        };
    }

    /**
     * Create a Stripe Checkout session for an app subscription.
     */
    public function checkout(Request $request, App $app, AppPricingPlan $plan)
    {
        $validated = $request->validate([
            'billing' => 'required|in:monthly,yearly',
        ]);

        $billing = $validated['billing'];

        $priceId = $billing === 'yearly'
            ? $plan->stripe_price_id_yearly
            : $plan->stripe_price_id_monthly;

        if (!$priceId) {
            return response()->json([
                'success' => false,
                'message' => 'Ce plan n\'est pas encore disponible pour la souscription en ligne.',
            ], 422);
        }

        $user = auth()->user();

        try {
            $customerId = $this->getOrCreateStripeCustomer($user);

            $session = StripeSession::create([
                'customer'             => $customerId,
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price'    => $priceId,
                    'quantity' => 1,
                ]],
                'mode'        => 'subscription',
                'success_url' => route('apps.show', $app->slug) . '?checkout=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('apps.show', $app->slug) . '?checkout=cancelled',
                'metadata'    => [
                    'user_id'         => $user->id,
                    'app_id'          => $app->id,
                    'app_slug'        => $app->slug,
                    'pricing_plan_id' => $plan->id,
                    'billing_period'  => $billing,
                    'type'            => 'app_subscription',
                ],
                'subscription_data' => [
                    'trial_period_days' => 14,
                    'metadata'          => [
                        'user_id'         => $user->id,
                        'app_id'          => $app->id,
                        'app_slug'        => $app->slug,
                        'pricing_plan_id' => $plan->id,
                        'billing_period'  => $billing,
                    ],
                ],
            ]);

            return response()->json([
                'success'      => true,
                'checkout_url' => $session->url,
            ]);
        } catch (\Exception $e) {
            Log::error('App checkout error', [
                'app'   => $app->slug,
                'plan'  => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du checkout : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get or create a Stripe customer for the given user.
     */
    private function getOrCreateStripeCustomer($user): string
    {
        $customers = \Stripe\Customer::search([
            'query' => "email:'{$user->email}'",
            'limit' => 1,
        ]);

        if (count($customers->data) > 0) {
            return $customers->data[0]->id;
        }

        $customer = \Stripe\Customer::create([
            'email'    => $user->email,
            'name'     => trim($user->first_name . ' ' . $user->last_name),
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        return $customer->id;
    }
}
