<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchtrendWatchShare extends Model
{
    protected $fillable = [
        'watch_id',
        'shared_by_user_id',
        'shared_with_email',
        'shared_with_user_id',
        'permission',
        'token',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function watch(): BelongsTo
    {
        return $this->belongsTo(WatchtrendWatch::class, 'watch_id');
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    public function sharedWith(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at');
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }
}
