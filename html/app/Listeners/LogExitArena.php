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
        $namespace = 'arena-party-'.$party.':*';
        Redis::del($namespace);
    }

}