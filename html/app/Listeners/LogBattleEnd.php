<?php

namespace App\Listeners;

use App\Events\BattleEnd;
use Illuminate\Support\Facades\Redis;

class LogBattleEnd
{

    public function __construct()
    {
        //
    }

    // 回合結束時清空雙方 action 集合，保留 battle 鎖定以進入下一回合
    public function handle(BattleEnd $event)
    {
        $party1 = $event->party1;
        $party2 = $event->party2;
        $room = $party1->classroom_id;
        // 清除我方該回合動作紀錄
        $namespace = 'arena:'.$room.':action:'.$party1->id;
        if (Redis::exists($namespace)) {
            Redis::del($namespace);
        }
        // 清除對方該回合動作紀錄
        $namespace = 'arena:'.$room.':action:'.$party2->id;
        if (Redis::exists($namespace)) {
            Redis::del($namespace);
        }
        // 設置新回合重置旗標（一次性）
        $namespace = 'arena:'.$room.':round:'.$party1->id;
        Redis::setex($namespace, 300, 1);
        $namespace = 'arena:'.$room.':round:'.$party2->id;
        Redis::setex($namespace, 300, 1);
        // 回合數 +1 並重置 30 秒倒數
        $namespace = 'arena:'.$room.':roundnum:'.$party1->id;
        if (Redis::exists($namespace)) {
            Redis::incr($namespace);
        } else {
            Redis::set($namespace, 1);
        }
        $namespace = 'arena:'.$room.':roundnum:'.$party2->id;
        if (Redis::exists($namespace)) {
            Redis::incr($namespace);
        } else {
            Redis::set($namespace, 1);
        }
        $namespace = 'arena:'.$room.':deadline:'.$party1->id;
        Redis::setex($namespace, 30, 1);
        $namespace = 'arena:'.$room.':deadline:'.$party2->id;
        Redis::setex($namespace, 30, 1);
    }

}