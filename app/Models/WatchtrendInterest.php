<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchtrendInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'watch_id',
        'name',
        'keywords',
        'priority',
        'context_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
    ];

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }
}
