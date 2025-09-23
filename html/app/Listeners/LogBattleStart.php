<?php

namespace App\Listeners;

use App\Events\BattleStart;
use Illuminate\Support\Facades\Redis;

class LogBattleStart
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
        Redis::setnx($namespace, $party2->id);
        $namespace = 'arena:'.$room.':battle:'.$party2->id;
        Redis::setnx($namespace, $party1->id);
        // 開戰時清空雙方 action 集合，確保回合乾淨開始
        $namespace = 'arena:'.$room.':action:'.$party1->id;
        if (Redis::exists($namespace)) {
            Redis::del($namespace);
        }
        $namespace = 'arena:'.$room.':action:'.$party2->id;
        if (Redis::exists($namespace)) {
            Redis::del($namespace);
        }
        // 設置首回合重置旗標（一次性）
        $namespace = 'arena:'.$room.':round:'.$party1->id;
        Redis::setex($namespace, 300, 1);
        $namespace = 'arena:'.$room.':round:'.$party2->id;
        Redis::setex($namespace, 300, 1);
        // 設置回合編號與倒數計時（雙方共用同一節奏）
        $namespace = 'arena:'.$room.':roundnum:'.$party1->id;
        Redis::set($namespace, 1);
        $namespace = 'arena:'.$room.':roundnum:'.$party2->id;
        Redis::set($namespace, 1);
        $namespace = 'arena:'.$room.':deadline:'.$party1->id;
        Redis::setex($namespace, 30, 1);
        $namespace = 'arena:'.$room.':deadline:'.$party2->id;
        Redis::setex($namespace, 30, 1);
    }

}