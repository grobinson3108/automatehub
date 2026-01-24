<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppUsageLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Only created_at

    protected $fillable = [
        'user_id',
        'app_id',
        'action',
        'credits_used',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the app for this usage
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    /**
     * Log a new usage event
     */
    public static function logUsage(int $userId, int $appId, string $action, int $creditsUsed = 1, ?array $metadata = null): self
    {
        return self::create([
            'user_id' => $userId,
            'app_id' => $appId,
            'action' => $action,
            'credits_used' => $creditsUsed,
            'metadata' => $metadata,
        ]);
    }
}
