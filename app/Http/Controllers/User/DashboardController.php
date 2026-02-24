<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AppUsageLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard (mini-apps HUB).
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Update last activity
            $user->update(['last_activity_at' => now()]);

            // Active app subscriptions (active or trial) with relations
            $userApps = $user->appSubscriptions()
                ->with(['app', 'pricingPlan'])
                ->whereIn('status', ['active', 'trial'])
                ->get();

            // IDs of apps already subscribed
            $subscribedAppIds = $userApps->pluck('app_id')->toArray();

            // Available apps the user has NOT subscribed to yet
            $availableApps = App::where('is_active', true)
                ->whereNotIn('id', $subscribedAppIds)
                ->with(['pricingPlans' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();

            // Recent app usage logs (last 7 days)
            $recentActivity = $user->appUsageLogs()
                ->with('app')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            // Stats
            $stats = [
                'active_apps'       => $userApps->count(),
                'credits_used_month' => $user->appUsageLogs()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->sum('credits_used'),
                'total_actions'     => $user->appUsageLogs()->count(),
            ];

            // Billing summary for active subscriptions
            $billing = [
                'monthly_total' => $userApps->sum(function ($sub) {
                    if (!$sub->pricingPlan) return 0;
                    return $sub->billing_period === 'yearly'
                        ? round($sub->pricingPlan->yearly_price / 12, 2)
                        : $sub->pricingPlan->monthly_price;
                }),
                'trial_count' => $userApps->filter(fn($sub) => $sub->onTrial())->count(),
                'paid_count'  => $userApps->filter(fn($sub) => $sub->isActive())->count(),
                'next_renewal' => $userApps
                    ->filter(fn($sub) => $sub->subscription_ends_at)
                    ->sortBy('subscription_ends_at')
                    ->first()?->subscription_ends_at,
            ];

            // Upsell: apps with available higher plans
            $upsellOpportunities = $userApps->filter(function ($sub) {
                if (!$sub->app || !$sub->pricingPlan) return false;
                $higherPlans = $sub->app->pricingPlans
                    ->where('is_active', true)
                    ->where('monthly_price', '>', $sub->pricingPlan->monthly_price);
                return $higherPlans->isNotEmpty();
            })->map(function ($sub) {
                $nextPlan = $sub->app->pricingPlans
                    ->where('is_active', true)
                    ->where('monthly_price', '>', $sub->pricingPlan->monthly_price)
                    ->sortBy('monthly_price')
                    ->first();
                return [
                    'app' => $sub->app,
                    'current_plan' => $sub->pricingPlan,
                    'next_plan' => $nextPlan,
                ];
            });

            // Onboarding: show if profile incomplete
            $showOnboarding = !$user->onboarding_completed;

            return view('user.dashboard.index', compact(
                'userApps',
                'availableApps',
                'recentActivity',
                'stats',
                'billing',
                'upsellOpportunities',
                'showOnboarding'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans User/DashboardController::index', ['error' => $e->getMessage()]);

            return view('user.dashboard.index', [
                'userApps'       => collect(),
                'availableApps'  => collect(),
                'recentActivity' => collect(),
                'stats'          => [
                    'active_apps'        => 0,
                    'credits_used_month' => 0,
                    'total_actions'      => 0,
                ],
                'billing'        => [
                    'monthly_total' => 0,
                    'trial_count'   => 0,
                    'paid_count'    => 0,
                    'next_renewal'  => null,
                ],
                'upsellOpportunities' => collect(),
                'showOnboarding' => false,
            ]);
        }
    }
}
