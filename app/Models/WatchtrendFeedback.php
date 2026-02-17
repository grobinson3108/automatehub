<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchtrendFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'watch_id',
        'analysis_id',
        'rating',
        'source_channel',
    ];

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(WatchtrendAnalysis::class, 'analysis_id');
    }
}
