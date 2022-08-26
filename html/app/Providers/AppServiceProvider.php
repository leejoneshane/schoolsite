<?php

namespace App\Providers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Menus;
use App\Models\User;
use App\Notifications\SyncCompletedNotification;
use App\Notifications\SyncADCompletedNotification;
use App\Notifications\SyncGsuiteCompletedNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Blade::component('menus', Menus::class);

        Blade::if('admin', function () {
            return auth()->user() && auth()->user()->is_admin;
        });
        Blade::if('teacher', function () {
            return auth()->user() && auth()->user()->user_type == 'Teacher';
        });
        Blade::if('student', function () {
            return auth()->user() && auth()->user()->user_type == 'Student';
        });

        Queue::after(function (JobProcessed $event) {
            if ($event->job->getName() == 'SyncFromTpedu') {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SyncCompletedNotification($event->job));
                }
            }
            if ($event->job->getName() == 'SyncToAd') {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SyncADCompletedNotification($event->job));
                }
            }
            if ($event->job->getName() == 'SyncToGsuite') {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SyncGsuiteCompletedNotification($event->job));
                }
            }
        });
    }
}
