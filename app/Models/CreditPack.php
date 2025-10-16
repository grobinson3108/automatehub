<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditPack extends Model
{
    protected $fillable = [
        'api_service_id',
        'name',
        'credits',
        'price',
        'discount_percentage',
        'stripe_price_id',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2'
    ];
    
    public function apiService(): BelongsTo
    {
        return $this->belongsTo(ApiService::class);
    }
    
    public function purchases(): HasMany
    {
        return $this->hasMany(CreditPurchase::class);
    }
    
    public function getDisplayPriceAttribute(): string
    {
        return number_format($this->price, 2) . '€';
    }
    
    public function getPricePerCreditAttribute(): float
    {
        return $this->credits > 0 ? $this->price / $this->credits : 0;
    }
    
    public function getOriginalPriceAttribute(): float
    {
        if ($this->discount_percentage > 0) {
            return $this->price / (1 - ($this->discount_percentage / 100));
        }
        return $this->price;
    }
    
    public function getSavingsAttribute(): float
    {
        return $this->original_price - $this->price;
    }
}