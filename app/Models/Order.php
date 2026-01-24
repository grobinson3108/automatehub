<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'pack_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'amount',
        'currency',
        'status',
        'customer_email',
        'customer_name',
        'download_count',
        'max_downloads',
        'delivered_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'download_count' => 'integer',
        'max_downloads' => 'integer',
        'delivered_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pack associated with the order.
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if downloads are still available.
     */
    public function canDownload(): bool
    {
        if (!$this->isCompleted()) {
            return false;
        }

        if ($this->download_count >= $this->max_downloads) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Increment the download count.
     */
    public function incrementDownload(): void
    {
        $this->increment('download_count');
    }

    /**
     * Mark the order as delivered.
     */
    public function markAsDelivered(): void
    {
        if (!$this->delivered_at) {
            $this->update([
                'delivered_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);
        }
    }

    /**
     * Mark the order as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
        $this->markAsDelivered();
    }

    /**
     * Mark the order as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Mark the order as refunded.
     */
    public function markAsRefunded(): void
    {
        $this->update(['status' => 'refunded']);
    }

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        $amount = $this->amount / 100;
        $symbol = $this->currency === 'USD' ? '$' : '€';
        return number_format($amount, 2) . $symbol;
    }

    /**
     * Get remaining downloads.
     */
    public function getRemainingDownloadsAttribute(): int
    {
        return max(0, $this->max_downloads - $this->download_count);
    }

    /**
     * Scope to get completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get orders by customer email.
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('customer_email', $email);
    }
}
