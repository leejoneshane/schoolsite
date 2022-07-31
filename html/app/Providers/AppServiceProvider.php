<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Menus;

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
    }
}
