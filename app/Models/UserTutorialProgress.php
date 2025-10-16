<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTutorialProgress extends Model
{
    use HasFactory;

    protected $table = 'user_tutorial_progress';

    protected $fillable = [
        'user_id',
        'tutorial_id',
        'completed_at',
        'progress_percentage',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tutorial(): BelongsTo
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function isCompleted(): bool
    {
        return $this->progress_percentage >= 100 && $this->completed_at !== null;
    }

    public function scopeCompleted($query)
    {
        return $query->where('progress_percentage', '>=', 100)
                    ->whereNotNull('completed_at');
    }

    public function scopeInProgress($query)
    {
        return $query->where('progress_percentage', '>', 0)
                    ->where('progress_percentage', '<', 100);
    }
}
