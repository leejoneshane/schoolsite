<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use App\Providers\TpeduServiceProvider;

class SyncFromTpedu implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $key = 'sync';
    public $timeout = 12000;
    public $only_expired = true;
    public $reset_password = false;
    public $sync_units = false;
    public $sync_classes = false;
    public $sync_subjects = false;
    public $remove_leave = false;
    public $start;
    public $end;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($only_expired, $password, $unit, $classroom, $subject, $remove)
    {
        $this->onQueue('app');
        $this->only_expired = $only_expired;
        $this->reset_password = $password;
        $this->sync_units = $unit;
        $this->sync_classes = $classroom;
        $this->sync_subjects = $subject;
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
        $this->start = time();
        $sso->sync_units($this->only_expired, $this->sync_units);
        $sso->sync_roles($this->only_expired, $this->sync_units);
        $sso->sync_subjects($this->only_expired, $this->sync_subjects);
        $sso->sync_classes($this->only_expired, $this->sync_classes);
        $sso->sync_teachers($this->only_expired, $this->reset_password, $this->remove_leave);
        $sso->sync_students($this->only_expired, $this->reset_password, $this->remove_leave);
        $this->end = time();
    }

    public function middleware()
    {
        return [(new WithoutOverlapping(self::$key))->dontRelease()];
    }
}
