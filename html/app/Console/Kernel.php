<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendNewsLetters;
use App\Jobs\GameDailyRenew;
use App\Jobs\GamePoisoned;

class Kernel extends ConsoleKernel
{
    protected function scheduleTimezone()
    {
        return 'Asia/Taipei';
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new SendNewsLetters)->daily();
        $schedule->job(new GameDailyRenew)->daily();
        $schedule->job(new GamePoisoned)->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
