<?php
namespace App\Services\WatchTrend;

use App\Models\WatchtrendWatch;
use App\Models\WatchtrendAnalysis;
use App\Models\UserAppSubscription;
use Illuminate\Support\Facades\Log;

class CreditService
{
    /**
     * Credits included per plan per month.
     */
    private array $planCredits = [
        'starter'  => 100,
        'pro'      => 500,
        'business' => 2000,
    ];

    /**
     * Check if user has enough credits for analysis.
     * For BYOK mode, always returns true (no credits needed).
     */
    public function hasCredits(WatchtrendWatch $watch, int $needed = 1): bool
    {
        if (($watch->ai_mode ?? 'byok') === 'byok') {
            return true;
        }

        $remaining = $this->getRemainingCredits($watch);
        return $remaining >= $needed;
    }

    /**
     * Get remaining credits for the current month.
     */
    public function getRemainingCredits(WatchtrendWatch $watch): int
    {
        $totalAllowed = $this->getMonthlyCredits($watch);
        $used = $this->getUsedCreditsThisMonth($watch);
        return max(0, $totalAllowed - $used);
    }

    /**
     * Get monthly credit allowance based on user's active plan.
     */
    public function getMonthlyCredits(WatchtrendWatch $watch): int
    {
        $plan = $this->getUserPlan($watch->user_id);
        return $this->planCredits[$plan] ?? $this->planCredits['starter'];
    }

    /**
     * Get managed-mode credits used this month for the watch.
     */
    public function getUsedCreditsThisMonth(WatchtrendWatch $watch): int
    {
        return (int) WatchtrendAnalysis::where('watch_id', $watch->id)
            ->where('ai_mode', 'managed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('credits_used');
    }

    /**
     * Determine user's plan by checking their active WatchTrend subscription.
     * Defaults to 'starter' if no active subscription found.
     */
    private function getUserPlan(int $userId): string
    {
        $subscription = UserAppSubscription::where('user_id', $userId)
            ->whereHas('app', fn($q) => $q->where('slug', 'watchtrend'))
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return 'starter';
        }

        $plan = $subscription->pricingPlan;
        if (!$plan) {
            return 'starter';
        }

        $planName = strtolower($plan->name ?? 'starter');

        if (str_contains($planName, 'business')) return 'business';
        if (str_contains($planName, 'pro')) return 'pro';
        return 'starter';
    }
}
