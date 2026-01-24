<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'tagline',
        'icon',
        'category',
        'features',
        'required_integrations',
        'is_active',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'required_integrations' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the pricing plans for this app
     */
    public function pricingPlans(): HasMany
    {
        return $this->hasMany(AppPricingPlan::class);
    }

    /**
     * Get the user subscriptions for this app
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserAppSubscription::class);
    }

    /**
     * Get the usage logs for this app
     */
    public function usageLogs(): HasMany
    {
        return $this->hasMany(AppUsageLog::class);
    }

    /**
     * Get the reviews for this app
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(AppReview::class);
    }

    /**
     * Check if app is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Check if app is in beta
     */
    public function isBeta(): bool
    {
        return $this->status === 'beta';
    }

    /**
     * Check if app is coming soon
     */
    public function isComingSoon(): bool
    {
        return $this->status === 'coming_soon';
    }
}
