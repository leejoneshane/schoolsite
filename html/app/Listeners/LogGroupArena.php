<?php
 
namespace App\Listeners;

use App\Events\GroupArena;
use Illuminate\Support\Facades\Redis;
 
class LogGroupArena
{

    public function __construct()
    {
        //
    }
 
    public function handle(GroupArena $event)
    {
        $party = $event->party->id;
        $namespace = 'arena-group:'.$party;
        $expire = 40 * 60; //40 mintues
        Redis::setex($namespace, $expire, $party);
    }

}