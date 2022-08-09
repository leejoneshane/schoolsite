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
    public $start;
    public $end;

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
        $google = new GsuiteServiceProvider;
        $this->start = time();
        $google->sync_teachers($this->password, $this->leave);
        $google->sync_students($this->password, $this->leave);
        $this->end = time();
    }

    public function middleware()
    {
        return [(new WithoutOverlapping(self::$key))->dontRelease()->expireAfter(1200)];
    }
}
