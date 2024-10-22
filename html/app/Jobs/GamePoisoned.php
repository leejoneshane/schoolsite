<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\GameCharacter;
use App\Models\GameMonsterSpawn;

class GamePoisoned implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 12000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $characters = GameCharacter::all();
        foreach ($characters as $c) {
            if ($c->buff == 'poisoned') {
                if ($c->effect_timeout >= Carbon::now()) {
                    $c->hp --;
                } else {
                    $c->effect_timeout = null;
                    $c->buff = null;
                }
                $c->save();
            }
        }
        $monsters = GameMonsterSpawn::all();
        foreach ($monsters as $c) {
            if ($c->buff == 'poisoned') {
                if ($c->effect_timeout >= Carbon::now()) {
                    $c->hp --;
                } else {
                    $c->effect_timeout = null;
                    $c->buff = null;
                }
                $c->save();
            }
        }
    }
}
