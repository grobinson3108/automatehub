<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'google_id',
        'avatar',
        'is_professional',
        'rgpd_accepted',
        'company_name',
        'company_address',
        'company_postal_code',
        'company_city',
        'company_country',
        'company_vat',
        'subscription_type',
        'subscription_expires_at',
        'onboarding_completed',
        'last_email_sent_at',
        'email_notifications',
        'weekly_digest',
        'level_n8n',
        'points',
        'last_activity_at',
        'quiz_completed_at',
        'role',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_professional' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Get the tutorials created by this user.
     */
    public function tutorials(): HasMany
    {
        return $this->hasMany(Tutorial::class, 'created_by');
    }

    /**
     * Get the downloads made by this user.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Get the user's tutorial progress.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(UserTutorialProgress::class);
    }

    /**
     * Get the user's favorite tutorials.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the badges earned by this user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    /**
     * Get the analytics events for this user.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(Analytics::class);
    }
    
    /**
     * Get the blog posts created by this user.
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'created_by');
    }

    /**
     * Check if user is premium.
     */
    public function isPremium(): bool
    {
        return $this->subscription_type === 'premium';
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if user is professional.
     */
    public function isProfessional(): bool
    {
        return $this->is_professional;
    }

    // ========== V2 APP SUBSCRIPTIONS ==========

    /**
     * Get the app subscriptions for this user (V2)
     */
    public function appSubscriptions(): HasMany
    {
        return $this->hasMany(UserAppSubscription::class);
    }

    /**
     * Get the app credentials for this user (V2)
     */
    public function appCredentials(): HasMany
    {
        return $this->hasMany(UserAppCredential::class);
    }

    /**
     * Get the app usage logs for this user (V2)
     */
    public function appUsageLogs(): HasMany
    {
        return $this->hasMany(AppUsageLog::class);
    }

    /**
     * Get the app reviews by this user (V2)
     */
    public function appReviews(): HasMany
    {
        return $this->hasMany(AppReview::class);
    }

    /**
     * Check if user has an active subscription for an app (V2)
     */
    public function hasAppAccess(string $appSlug): bool
    {
        return $this->appSubscriptions()
            ->whereHas('app', function ($query) use ($appSlug) {
                $query->where('slug', $appSlug);
            })
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere(function ($q) {
                          $q->where('status', 'trial')
                            ->where('trial_ends_at', '>', now());
                      });
            })
            ->exists();
    }

    /**
     * Get user's subscription for a specific app (V2)
     */
    public function getAppSubscription(string $appSlug): ?UserAppSubscription
    {
        return $this->appSubscriptions()
            ->whereHas('app', function ($query) use ($appSlug) {
                $query->where('slug', $appSlug);
            })
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere(function ($q) {
                          $q->where('status', 'trial')
                            ->where('trial_ends_at', '>', now());
                      });
            })
            ->first();
    }

    /**
     * Get credentials for a specific app and service (V2)
     */
    public function getAppCredential(string $appSlug, string $service): ?UserAppCredential
    {
        return $this->appCredentials()
            ->where('app_slug', $appSlug)
            ->where('service', $service)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if user has configured credentials for an app (V2)
     */
    public function hasAppCredentials(string $appSlug): bool
    {
        // Récupérer l'app pour voir ses required_integrations
        $app = \App\Models\App::where('slug', $appSlug)->first();

        if (!$app || empty($app->required_integrations)) {
            return true; // No credentials required
        }

        // Vérifier que tous les credentials requis sont présents
        foreach ($app->required_integrations as $service) {
            if (!$this->getAppCredential($appSlug, $service)) {
                return false;
            }
        }

        return true;
    }
}
