<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchtrendUserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ai_mode',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_paused',
        'onboarding_completed',
        'summary_language',
        'items_per_page',
    ];

    protected $casts = [
        'telegram_paused' => 'boolean',
        'onboarding_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
