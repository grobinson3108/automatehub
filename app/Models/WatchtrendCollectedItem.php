<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WatchtrendCollectedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'watch_id',
        'external_id',
        'url',
        'title',
        'content',
        'author',
        'published_at',
        'metadata',
        'content_hash',
        'is_analyzed',
        'is_read',
    ];

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'datetime',
        'is_analyzed' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(WatchtrendSource::class, 'source_id');
    }

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(WatchtrendAnalysis::class, 'collected_item_id');
    }
}
