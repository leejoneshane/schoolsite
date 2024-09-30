<?php
 
namespace App\Listeners;
 
use App\Events\ExitArena;
use Illuminate\Support\Facades\Redis;
 
class LogExitArena
{

    public function __construct()
    {
        //
    }
 
    public function handle(ExitArena $event)
    {
        $party = $event->character->party_id;
        $namespace = 'arena-party-'.$party.':'.$event->character->uuid;
        Redis::del($namespace);
        $namespace = 'arena-group:'.$event->character->party_id;
        Redis::del($namespace);
    }

}