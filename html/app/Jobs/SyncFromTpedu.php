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
use Illuminate\Support\Facades\DB;

class SyncFromTpedu implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $key = 'sync';
    public $timeout = 12000;
    public $start;
    public $end; 

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

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
        $sso->sync_units();
        $sso->sync_subjects();
        $sso->sync_classes();
        $sso->sync_roles();
        $sso->sync_teachers();
        $classes = DB::table('classrooms')->get();
        foreach ($classes as $cls) {
            $sso->sync_students($cls->id);
        }
        $this->end = time();
        $this->release(10);
    }

    public function middleware()
    {
        return [new WithoutOverlapping(self::$key)];
    }
}
