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
    public $only_expired = false;
    public $start;
    public $end;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($only_expired)
    {
        $this->onQueue('app');
        $this->only_expired = $only_expired;
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
        $sso->sync_units($this->only_expired);
        $sso->sync_roles($this->only_expired);
        $sso->sync_subjects($this->only_expired);
        $sso->sync_classes($this->only_expired);
        $sso->sync_teachers($this->only_expired);
        $sso->sync_students($this->only_expired);
        $this->end = time();
    }

    public function middleware()
    {
        return [(new WithoutOverlapping(self::$key))->dontRelease()->expireAfter(1200)];
    }
}
