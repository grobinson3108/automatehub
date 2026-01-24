<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class UserAppCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'app_slug',
        'service',
        'type',
        'credentials',
        'is_active',
        'expires_at',
        'last_verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'credentials', // Ne jamais exposer les credentials en JSON
    ];

    /**
     * Get the user that owns the credential
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get decrypted credentials
     */
    public function getDecryptedCredentials(): array
    {
        return json_decode(Crypt::decryptString($this->credentials), true);
    }

    /**
     * Set encrypted credentials
     */
    public function setCredentials(array $credentials): void
    {
        $this->credentials = Crypt::encryptString(json_encode($credentials));
    }

    /**
     * Check if credential is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if credential needs verification
     */
    public function needsVerification(): bool
    {
        if (!$this->last_verified_at) {
            return true;
        }

        // Vérifier tous les 7 jours
        return $this->last_verified_at->addDays(7)->isPast();
    }
}
