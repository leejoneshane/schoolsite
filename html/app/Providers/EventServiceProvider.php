<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use App\Listeners\SendJobDoneNotification;
use App\Notifications\SyncCompletedNotification;
use App\Notifications\SyncADCompletedNotification;
use App\Notifications\SyncGsuiteCompletedNotification;
use App\Models\User;
use \SocialiteProviders\Google\GoogleExtendSocialite;
use \SocialiteProviders\Facebook\FacebookExtendSocialite;
use \SocialiteProviders\Yahoo\YahooExtendSocialite;
use \SocialiteProviders\Line\LineExtendSocialite;
use \SocialiteProviders\Manager\SocialiteWasCalled;
use App\Jobs\SyncFromTpedu;
use App\Jobs\SyncToAD;
use App\Jobs\SyncToGoogle;

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
//        JobProcessed::class => [
//            SendJobDoneNotification::class,
//        ],
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
        Queue::after(function (JobProcessed $event) {
            if ($event->job instanceof SyncFromTpedu) {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SyncCompletedNotification($event->job));
                }
            }
            if ($event->job instanceof SyncToAD) {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SyncADCompletedNotification($event->job));
                }
            }
            if ($event->job instanceof SyncToGoogle) {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SyncGsuiteCompletedNotification($event->job));
                }
            }
        });
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
