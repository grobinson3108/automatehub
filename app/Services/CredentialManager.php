<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAppCredential;
use Illuminate\Support\Facades\Crypt;

class CredentialManager
{
    /**
     * Store encrypted credentials for a user
     */
    public function store(User $user, string $appSlug, string $service, string $type, array $credentials): UserAppCredential
    {
        $encryptedCredentials = Crypt::encryptString(json_encode($credentials));

        return UserAppCredential::updateOrCreate(
            [
                'user_id' => $user->id,
                'app_slug' => $appSlug,
                'service' => $service,
            ],
            [
                'type' => $type,
                'credentials' => $encryptedCredentials,
                'is_active' => true,
                'last_verified_at' => now(),
            ]
        );
    }

    /**
     * Get decrypted credentials
     */
    public function get(User $user, string $appSlug, string $service): ?array
    {
        $credential = UserAppCredential::where('user_id', $user->id)
            ->where('app_slug', $appSlug)
            ->where('service', $service)
            ->where('is_active', true)
            ->first();

        if (!$credential) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($credential->credentials);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            \Log::error("Failed to decrypt credentials for user {$user->id}, app {$appSlug}, service {$service}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete credentials
     */
    public function delete(User $user, string $appSlug, string $service): bool
    {
        return UserAppCredential::where('user_id', $user->id)
            ->where('app_slug', $appSlug)
            ->where('service', $service)
            ->delete() > 0;
    }

    /**
     * Verify credentials (test connection)
     */
    public function verify(User $user, string $appSlug, string $service): bool
    {
        $credential = UserAppCredential::where('user_id', $user->id)
            ->where('app_slug', $appSlug)
            ->where('service', $service)
            ->first();

        if (!$credential) {
            return false;
        }

        // TODO: Implement actual verification logic per service
        // For now, just update last_verified_at
        $credential->update(['last_verified_at' => now()]);

        return true;
    }

    /**
     * Check if credentials are expired (for OAuth tokens)
     */
    public function isExpired(User $user, string $appSlug, string $service): bool
    {
        $credential = UserAppCredential::where('user_id', $user->id)
            ->where('app_slug', $appSlug)
            ->where('service', $service)
            ->first();

        if (!$credential || !$credential->expires_at) {
            return false;
        }

        return $credential->expires_at->isPast();
    }

    /**
     * Refresh OAuth token
     */
    public function refreshToken(User $user, string $appSlug, string $service): bool
    {
        // TODO: Implement OAuth token refresh logic per service
        // This will be service-specific (Instagram, TikTok, etc.)

        return true;
    }

    /**
     * Get all credentials for an app
     */
    public function getAllForApp(User $user, string $appSlug): array
    {
        $credentials = UserAppCredential::where('user_id', $user->id)
            ->where('app_slug', $appSlug)
            ->where('is_active', true)
            ->get();

        $result = [];
        foreach ($credentials as $credential) {
            $result[$credential->service] = [
                'type' => $credential->type,
                'is_active' => $credential->is_active,
                'expires_at' => $credential->expires_at,
                'last_verified_at' => $credential->last_verified_at,
                'needs_refresh' => $credential->isExpired(),
            ];
        }

        return $result;
    }
}
