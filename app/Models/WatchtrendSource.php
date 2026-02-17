<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WatchtrendSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'watch_id',
        'type',
        'name',
        'config',
        'status',
        'error_message',
        'error_count',
        'last_collected_at',
        'items_collected_total',
    ];

    protected $casts = [
        'config' => 'array',
        'last_collected_at' => 'datetime',
    ];

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }

    public function collectedItems(): HasMany
    {
        return $this->hasMany(WatchtrendCollectedItem::class, 'source_id');
    }
}
