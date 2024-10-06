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

    //使用 redis set 資料類型
    public function handle(GroupArena $event)
    {
        $party = $event->party;
        $room = $party->classroom_id;
        $namespace = 'arena:'.$room.':ready';
        Redis::sadd($namespace, $party->id);
    }

}