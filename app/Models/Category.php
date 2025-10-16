<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_premium',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_premium' => 'boolean',
    ];

    /**
     * Get the tutorials for this category.
     */
    public function tutorials(): HasMany
    {
        return $this->hasMany(Tutorial::class);
    }

    /**
     * Get published tutorials for this category.
     */
    public function publishedTutorials(): HasMany
    {
        return $this->hasMany(Tutorial::class)->published();
    }

    /**
     * Scope a query to only include free categories.
     */
    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Scope a query to only include premium categories.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Check if category is premium.
     */
    public function isPremium(): bool
    {
        return $this->is_premium;
    }

    /**
     * Get the count of published tutorials in this category.
     */
    public function getPublishedTutorialsCountAttribute(): int
    {
        return $this->publishedTutorials()->count();
    }
}
