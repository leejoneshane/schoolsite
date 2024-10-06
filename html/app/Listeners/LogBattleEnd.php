<?php

namespace App\Listeners;

use App\Events\BattleStart;
use Illuminate\Support\Facades\Redis;

class LogBattleEnd
{

    public function __construct()
    {
        //
    }

    //使用 redis string 資料類型
    public function handle(BattleStart $event)
    {
        $party1 = $event->party1;
        $party2 = $event->party2;
        $room = $party1->classroom_id;
        $namespace = 'arena:'.$room.':battle:'.$party1->id;
        Redis::del($namespace);
        $namespace = 'arena:'.$room.':battle:'.$party2->id;
        Redis::del($namespace);
    }

}