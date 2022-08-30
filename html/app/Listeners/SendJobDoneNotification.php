<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Events\JobProcessed;
use App\Models\User;
use App\Jobs\SyncFromTpedu;
use App\Jobs\SyncToAD;
use App\Jobs\SyncToGoogle;
use App\Notifications\SyncCompletedNotification;
use App\Notifications\SyncADCompletedNotification;
use App\Notifications\SyncGsuiteCompletedNotification;

class SendJobDoneNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 5;

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    public function __construct()
    {
        //
    }

    public function handle(JobProcessed $event)
    {
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
    }
}
