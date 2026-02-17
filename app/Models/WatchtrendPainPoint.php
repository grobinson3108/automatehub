<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchtrendPainPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'watch_id',
        'title',
        'description',
        'priority',
        'status',
    ];

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
