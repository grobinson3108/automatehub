<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutorial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'category_id',
        'required_level',
        'target_audience',
        'subscription_required',
        'files',
        'tags',
        'published_at',
        'is_draft',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'files' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_draft' => 'boolean',
        'subscription_required' => 'string',
    ];

    /**
     * Get the user who created this tutorial.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the category this tutorial belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the downloads for this tutorial.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Get the favorites for this tutorial.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the progress records for this tutorial.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(UserTutorialProgress::class);
    }

    /**
     * Get the analytics events for this tutorial.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(Analytics::class);
    }

    /**
     * Scope a query to only include published tutorials.
     */
    public function scopePublished($query)
    {
        return $query->where('is_draft', false)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft tutorials.
     */
    public function scopeDraft($query)
    {
        return $query->where('is_draft', true);
    }

    /**
     * Scope a query to filter by required level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('required_level', $level);
    }

    /**
     * Scope a query to filter by target audience.
     */
    public function scopeByAudience($query, $audience)
    {
        return $query->where('target_audience', $audience);
    }

    /**
     * Scope a query to filter by subscription type required.
     */
    public function scopeBySubscriptionRequired($query, $subscriptionType)
    {
        return $query->where('subscription_required', $subscriptionType);
    }

    /**
     * Check if tutorial is published.
     */
    public function isPublished(): bool
    {
        return !$this->is_draft && 
               $this->published_at !== null && 
               $this->published_at <= now();
    }

    /**
     * Check if tutorial is premium (based on category).
     */
    public function isPremium(): bool
    {
        return $this->category && $this->category->is_premium;
    }

    /**
     * Check if tutorial requires premium subscription.
     */
    public function requiresPremium(): bool
    {
        return $this->subscription_required === 'premium';
    }

    /**
     * Check if tutorial requires pro subscription.
     */
    public function requiresPro(): bool
    {
        return $this->subscription_required === 'pro';
    }

    /**
     * Check if tutorial is free.
     */
    public function isFree(): bool
    {
        return $this->subscription_required === 'free';
    }
}
