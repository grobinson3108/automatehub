<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WatchtrendWatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'icon',
        'collection_frequency',
        'digest_frequency',
        'digest_hour',
        'telegram_enabled',
        'ai_mode',
        'summary_language',
        'show_low_relevance',
        'status',
        'calibration_completed_at',
        'last_collected_at',
        'last_digest_sent_at',
        'items_per_page',
        'sort_order',
    ];

    protected $casts = [
        'telegram_enabled' => 'boolean',
        'show_low_relevance' => 'boolean',
        'calibration_completed_at' => 'datetime',
        'last_collected_at' => 'datetime',
        'last_digest_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interests(): HasMany
    {
        return $this->hasMany(WatchtrendInterest::class, 'watch_id');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(WatchtrendSource::class, 'watch_id');
    }

    public function collectedItems(): HasMany
    {
        return $this->hasMany(WatchtrendCollectedItem::class, 'watch_id');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(WatchtrendAnalysis::class, 'watch_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(WatchtrendFeedback::class, 'watch_id');
    }

    public function painPoints(): HasMany
    {
        return $this->hasMany(WatchtrendPainPoint::class, 'watch_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
