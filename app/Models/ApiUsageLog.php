<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'subscription_id',
        'endpoint',
        'method',
        'credits_used',
        'response_code',
        'response_time_ms',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];
    
    public $timestamps = false;
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($log) {
            if (!$log->created_at) {
                $log->created_at = now();
            }
        });
    }
    
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserApiSubscription::class, 'subscription_id');
    }
    
    public function wasSuccessful(): bool
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }
    
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('response_code', [200, 299]);
    }
    
    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('response_code')
              ->orWhere('response_code', '<', 200)
              ->orWhere('response_code', '>=', 300);
        });
    }
    
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
                     ->whereMonth('created_at', now()->month);
    }
}