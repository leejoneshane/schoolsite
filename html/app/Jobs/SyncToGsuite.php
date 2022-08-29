<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use App\Providers\GsuiteServiceProvider;

class SyncToGsuite implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $key = 'sync';
    public $timeout = 12000;
    public $password;
    public $leave;
    public $target;
    public $start;
    public $end;
    public $log = [];

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
        $this->start = time();
        if ($this->leave == 'onduty') {
            if ($this->target == 'teachers') {
                $this->log = $google->sync_teachers($this->password);
            } elseif (substr($this->target, 0, 5) == 'grade') {
                $grade = substr($this->target, -1);
                $this->log = $google->sync_grade($grade, $this->password);
            } else {
                $this->log = $google->sync_class($this->target, $this->password);
            }
        } else {
            $this->log = $google->deal_graduate($this->leave);
        }
        $this->end = time();
    }

    public function middleware()
    {
        return [(new WithoutOverlapping(self::$key))->dontRelease()];
    }
}
