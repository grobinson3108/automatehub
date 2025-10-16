<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserApiSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'api_service_id',
        'pricing_plan_id',
        'api_key',
        'monthly_quota',
        'used_this_month',
        'extra_credits',
        'reset_date',
        'status',
        'trial_ends_at'
    ];
    
    protected $casts = [
        'reset_date' => 'date',
        'trial_ends_at' => 'datetime'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($subscription) {
            if (!$subscription->api_key) {
                $subscription->api_key = $subscription->generateApiKey();
            }
            if (!$subscription->reset_date) {
                $subscription->reset_date = now()->addMonth();
            }
        });
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function apiService(): BelongsTo
    {
        return $this->belongsTo(ApiService::class);
    }
    
    public function pricingPlan(): BelongsTo
    {
        return $this->belongsTo(ApiPricingPlan::class, 'pricing_plan_id');
    }
    
    public function usageLogs(): HasMany
    {
        return $this->hasMany(ApiUsageLog::class, 'subscription_id');
    }
    
    public function creditPurchases(): HasMany
    {
        return $this->hasMany(CreditPurchase::class, 'subscription_id');
    }
    
    public function generateApiKey(): string
    {
        do {
            $key = Str::random(32);
        } while (static::where('api_key', $key)->exists());
        
        return $key;
    }
    
    public function getRemainingCreditsAttribute(): int
    {
        return max(0, ($this->monthly_quota + $this->extra_credits) - $this->used_this_month);
    }
    
    public function canMakeRequest(int $creditsNeeded = 1): bool
    {
        return $this->status === 'active' && $this->remaining_credits >= $creditsNeeded;
    }
    
    public function useCredits(int $credits = 1): bool
    {
        if (!$this->canMakeRequest($credits)) {
            return false;
        }
        
        $this->increment('used_this_month', $credits);
        return true;
    }
    
    public function resetMonthlyUsage(): void
    {
        $this->update([
            'used_this_month' => 0,
            'reset_date' => now()->addMonth()
        ]);
    }
    
    public function isTrialing(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
    
    public function addExtraCredits(int $credits): void
    {
        $this->increment('extra_credits', $credits);
    }
}