<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppPricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'name',
        'monthly_price',
        'yearly_price',
        'features',
        'limits',
        'sort_order',
        'is_active',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the app that owns the pricing plan
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    /**
     * Get the subscriptions for this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserAppSubscription::class, 'pricing_plan_id');
    }

    /**
     * Get yearly discount percentage
     */
    public function getYearlyDiscountAttribute(): ?int
    {
        if (!$this->yearly_price || !$this->monthly_price) {
            return null;
        }

        $yearlyEquivalent = $this->monthly_price * 12;
        $discount = (($yearlyEquivalent - $this->yearly_price) / $yearlyEquivalent) * 100;

        return round($discount);
    }

    /**
     * Get monthly savings with yearly plan
     */
    public function getMonthlySavingsAttribute(): ?float
    {
        if (!$this->yearly_price || !$this->monthly_price) {
            return null;
        }

        $monthlyEquivalent = $this->yearly_price / 12;
        return round($this->monthly_price - $monthlyEquivalent, 2);
    }
}
