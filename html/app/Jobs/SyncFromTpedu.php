<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Providers\TpeduServiceProvider;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SyncCompletedNotification;
use App\Events\AdminMessage;

class SyncFromTpedu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $key = 'sync';
    public $timeout = 12000;
    public $only_expired = true;
    public $reset_password = false;
    public $sync_units = false;
    public $sync_classes = false;
    public $sync_subjects = false;
    public $sync_teachers = false;
    public $sync_students = false;
    public $sync_target = false;
    public $remove_leave = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($only_expired, $password, $unit, $classroom, $subject, $teacher, $student, $target, $remove)
    {
        $this->onQueue('app');
        $this->only_expired = $only_expired;
        $this->reset_password = $password;
        $this->sync_units = $unit;
        $this->sync_classes = $classroom;
        $this->sync_subjects = $subject;
        $this->sync_teachers = $teacher;
        $this->sync_students = $student;
        $this->sync_target = $target;
        $this->remove_leave = $remove;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sso = new TpeduServiceProvider;
        $start_time = Carbon::now()->format('Y-m-d H:m:s');
        $logs[] = $sso->sync_units($this->only_expired, $this->sync_units);
        $logs[] = $sso->sync_roles($this->only_expired, $this->sync_units);
        $logs[] = $sso->sync_subjects($this->only_expired, $this->sync_subjects);
        $logs[] = $sso->sync_classes($this->only_expired, $this->sync_classes);
        $logs[] = $sso->sync_teachers($this->only_expired, $this->sync_teachers, $this->reset_password, $this->remove_leave);
        if ($this->sync_students) {
            if ($this->sync_target == 'students') {
                $logs[] = $sso->sync_students($this->only_expired, $this->reset_password, $this->remove_leave);
            } else if (substr($this->sync_target, 0, 5) == 'grade') {
                $grade = substr($this->sync_target, -1);
                $logs[] = $sso->sync_students_for_grade($grade, $this->only_expired, $this->reset_password, $this->remove_leave);
            } else {
                $logs[] = $sso->sync_students_for_class($this->sync_target, $this->only_expired, $this->reset_password, $this->remove_leave);
            }
        }
        $detail_log = [];
        for ($i = 0; $i < count($logs); $i++) {
            if (isset($logs[$i]) && !empty($logs[$i])) {
                for ($j = 0; $j < count($logs[$i]); $j++) {
                    $detail_log[] = $logs[$i][$j];
                  }
            }
        }
        $end_time = Carbon::now()->format('Y-m-d H:m:s');
        $admins = User::admins();
        broadcast(new AdminMessage("資料庫同步作業於 $start_time 開始進行，已經於 $end_time 順利完成！"));
        Notification::sendNow($admins, new SyncCompletedNotification('SyncFromTpedu', $start_time, $end_time, $detail_log));
    }
}
