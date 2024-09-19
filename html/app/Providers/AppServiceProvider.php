<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Menus;
use App\View\Components\Messager;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Jenssegers\Agent\Agent;

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

        Blade::component('messager', Messager::class);

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
        Blade::if('adminorteacher', function () {
            return auth()->user() && (auth()->user()->is_admin || auth()->user()->user_type == 'Teacher');
        });

        Blade::if('mobile', function () {
            return (new Agent)->isMobile();
        });

        Blade::if('desktop', function () {
            return !((new Agent)->isMobile());
        });

        Blade::if('locked', function ($room_id = null) {
            return locked($room_id);
        });

        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });
        Queue::after(function (JobProcessed $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });
    }
}
