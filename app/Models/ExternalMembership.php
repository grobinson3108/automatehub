<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalMembership extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'external_id',
        'email',
        'status',
        'benefits',
        'expires_at'
    ];
    
    protected $casts = [
        'benefits' => 'array',
        'expires_at' => 'datetime'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }
    
    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
    
    public function getApiCreditsAttribute(): array
    {
        return $this->benefits['api_credits'] ?? [];
    }
    
    public function hasApiAccess(string $apiSlug): bool
    {
        $credits = $this->api_credits;
        return isset($credits[$apiSlug]) && $credits[$apiSlug] > 0;
    }
}