<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use \SocialiteProviders\Google\GoogleExtendSocialite;
use \SocialiteProviders\Facebook\FacebookExtendSocialite;
use \SocialiteProviders\Yahoo\YahooExtendSocialite;
use \SocialiteProviders\Line\LineExtendSocialite;
use \SocialiteProviders\Manager\SocialiteWasCalled;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SocialiteWasCalled::class => [
            GoogleExtendSocialite::class,
            FacebookExtendSocialite::class,
            YahooExtendSocialite::class,
            LineExtendSocialite::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
