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

    //使用 redis set 資料類型
    public function handle(ExitArena $event)
    {
        $char = $event->character;
        $uuid = $char->uuid;
        $party = $char->party_id;
        $room = $char->party->classroom_id;
        $namespace = 'arena:'.$room.':party:'.$party;
        Redis::srem($namespace, $uuid);
        $namespace = 'arena:'.$room.':ready';
        Redis::srem($namespace, $party);
    }

}