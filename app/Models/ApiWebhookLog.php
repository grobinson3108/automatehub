<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiWebhookLog extends Model
{
    protected $fillable = [
        'source',
        'event_type',
        'payload',
        'status',
        'error_message',
        'processed_at'
    ];
    
    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime'
    ];
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }
    
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    public function markAsProcessed(): void
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now()
        ]);
    }
    
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'processed_at' => now()
        ]);
    }
}