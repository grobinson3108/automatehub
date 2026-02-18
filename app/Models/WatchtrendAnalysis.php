<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WatchtrendAnalysis extends Model
{
    use HasFactory;

    protected $table = 'watchtrend_analyses';

    const UPDATED_AT = null;

    protected $fillable = [
        'collected_item_id',
        'watch_id',
        'relevance_score',
        'category',
        'summary_fr',
        'actionable_insight',
        'matching_interests',
        'key_takeaways',
        'ai_model',
        'ai_mode',
        'tokens_used',
        'credits_used',
        'is_favorite',
    ];

    protected $casts = [
        'matching_interests' => 'array',
        'key_takeaways' => 'array',
        'relevance_score' => 'decimal:2',
        'is_favorite' => 'boolean',
    ];

    public function collectedItem(): BelongsTo
    {
        return $this->belongsTo(WatchtrendCollectedItem::class, 'collected_item_id');
    }

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(WatchtrendFeedback::class, 'analysis_id');
    }
}
