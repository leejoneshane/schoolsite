<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Providers\GsuiteServiceProvider;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SyncCompletedNotification;
use App\Events\AdminMessage;

class SyncToGoogle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $key = 'syncGoogle';
    public $timeout = 12000;
    public $password;
    public $leave;
    public $target;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($password, $leave, $target = false)
    {
        $this->onQueue('app');
        $this->password = $password;
        $this->leave = $leave;
        $this->target = $target;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $google = new GsuiteServiceProvider;
        $start_time = Carbon::now()->format('Y-m-d H:m:s l');
        if ($this->leave == 'onduty') {
            if ($this->target == 'teachers') {
                $logs = $google->sync_teachers($this->password);
            } elseif (substr($this->target, 0, 5) == 'grade') {
                $grade = substr($this->target, -1);
                $logs = $google->sync_grade($grade, $this->password);
            } else {
                $logs = $google->sync_class($this->target, $this->password);
            }
        } else {
            $logs = $google->deal_graduate($this->leave);
        }
        $end_time = Carbon::now()->format('Y-m-d H:m:s l');
        $admins = User::admins();
        Notification::sendNow($admins, new SyncCompletedNotification('SyncToGoogle', $start_time, $end_time, $logs));
        broadcast(new AdminMessage("Gsuite 同步作業於 $start_time 開始進行，已經於 $end_time 順利完成！"));
    }
}
