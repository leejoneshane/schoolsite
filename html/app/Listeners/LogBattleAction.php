<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Redis;
use App\Events\BattleAction;

class LogBattleAction
{

    public function __construct()
    {
        //
    }

    //使用 redis set 資料類型
    public function handle(BattleAction $event)
    {
        $party = $event->party;
        $room = $party->classroom_id;
        $namespace = 'arena:'.$room.':action:'.$party->id;
        Redis::sadd($namespace, $event->message);
    }

}