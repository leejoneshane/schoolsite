<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Jobs\SyncFromTpedu;
use App\Jobs\SyncToAd;
use App\Jobs\SyncToGsuite;
use App\Notifications\SyncCompletedNotification;
use App\Notifications\SyncADCompletedNotification;
use App\Notifications\SyncGsuiteCompletedNotification;

class SendJobDoneNotification
{

    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        if ($event->job instanceof SyncFromTpedu) {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(new SyncCompletedNotification($event->job));
            }
        }
        if ($event->job instanceof SyncToAd) {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(new SyncADCompletedNotification($event->job));
            }
        }
        if ($event->job instanceof SyncToGsuite) {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(new SyncGsuiteCompletedNotification($event->job));
            }
        }
    }
}
