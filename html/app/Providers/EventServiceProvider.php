<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Events\EnterArena;
use App\Events\ExitArena;
use App\Events\GroupArena;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\LogEnterArena;
use App\Listeners\LogExitArena;
use App\Listeners\LogGroupArena;
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
        Login::class => [
            LogSuccessfulLogin::class,
        ],
        Logout::class => [
            LogSuccessfulLogout::class,
        ],
        EnterArena::class => [
            LogEnterArena::class,
        ],
        ExitArena::class => [
            LogExitArena::class,
        ],
        GroupArena::class => [
            LogGroupArena::class,
        ],
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
