<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiService extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'category',
        'features',
        'is_active',
        'endpoint_base',
        'node_package',
        'default_quota'
    ];
    
    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function pricingPlans(): HasMany
    {
        return $this->hasMany(ApiPricingPlan::class)->orderBy('sort_order');
    }
    
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserApiSubscription::class);
    }
    
    public function creditPacks(): HasMany
    {
        return $this->hasMany(CreditPack::class)->where('is_active', true);
    }
    
    public function getFreePlan()
    {
        return $this->pricingPlans()->where('monthly_price', 0)->first();
    }
    
    public function generateApiKey(): string
    {
        return bin2hex(random_bytes(32));
    }
}