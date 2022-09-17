<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Providers\ADServiceProvider;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SyncCompletedNotification;
use App\Events\AdminMessage;

class SyncToAD implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $key = 'syncAD';
    public $timeout = 12000;
    public $password;
    public $leave;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($password, $leave)
    {
        $this->onQueue('app');
        $this->password = $password;
        $this->leave = $leave;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ad = new ADServiceProvider;
        $start_time = Carbon::now()->format('Y-m-d H:m:s l');
        $logs = $ad->sync_teachers($this->password, $this->leave);
        $end_time = Carbon::now()->format('Y-m-d H:m:s l');
        $admins = User::admins();
        Notification::sendNow($admins, new SyncCompletedNotification('SyncToAD', $start_time, $end_time, $logs));
        broadcast(new AdminMessage("AD 同步作業於 $start_time 開始進行，已經於 $end_time 順利完成！"));
    }
}
