<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiPricingPlan extends Model
{
    protected $fillable = [
        'api_service_id',
        'name',
        'monthly_price',
        'monthly_quota',
        'extra_credit_price',
        'features',
        'sort_order',
        'is_active'
    ];
    
    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'monthly_price' => 'decimal:2',
        'extra_credit_price' => 'decimal:4'
    ];
    
    public function apiService(): BelongsTo
    {
        return $this->belongsTo(ApiService::class);
    }
    
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserApiSubscription::class, 'pricing_plan_id');
    }
    
    public function isFree(): bool
    {
        return $this->monthly_price == 0;
    }
    
    public function getDisplayPriceAttribute(): string
    {
        if ($this->isFree()) {
            return 'Gratuit';
        }
        return number_format($this->monthly_price, 2) . '€/mois';
    }
    
    public function getCreditsPerEuroAttribute(): float
    {
        if ($this->monthly_price == 0) {
            return 0;
        }
        return $this->monthly_quota / $this->monthly_price;
    }
}