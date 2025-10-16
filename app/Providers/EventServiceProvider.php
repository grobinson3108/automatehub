<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Events\TutorialCompleted;
use App\Events\DownloadCompleted;
use App\Listeners\SendWelcomeEmailAndAwardBadges;
use App\Listeners\CheckBadgesAndTrackAnalytics;
use App\Listeners\TrackDownloadAndCheckLimits;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeEmailAndAwardBadges::class,
        ],
        
        TutorialCompleted::class => [
            CheckBadgesAndTrackAnalytics::class,
        ],
        
        DownloadCompleted::class => [
            TrackDownloadAndCheckLimits::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
