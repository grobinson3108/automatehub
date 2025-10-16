<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'credit_pack_id',
        'credits',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_data'
    ];
    
    protected $casts = [
        'payment_data' => 'array',
        'amount' => 'decimal:2'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserApiSubscription::class, 'subscription_id');
    }
    
    public function creditPack(): BelongsTo
    {
        return $this->belongsTo(CreditPack::class);
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    
    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
        
        // Ajouter les crédits à la subscription
        if ($this->subscription) {
            $this->subscription->addExtraCredits($this->credits);
        }
    }
    
    public function markAsFailed(string $reason = null): void
    {
        $data = $this->payment_data ?? [];
        $data['failure_reason'] = $reason;
        
        $this->update([
            'status' => 'failed',
            'payment_data' => $data
        ]);
    }
}