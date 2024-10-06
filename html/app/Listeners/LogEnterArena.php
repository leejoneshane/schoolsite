<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Redis;
use App\Events\EnterArena;
use App\Events\GroupArena;

class LogEnterArena
{

    public function __construct()
    {
        //
    }

    //使用 redis set 資料類型
    public function handle(EnterArena $event)
    {
        $this->party = $event->character->party;
        $char = $event->character;
        $uuid = $char->uuid;
        $party = $char->party_id;
        $room = $char->party->classroom_id;
        $namespace = 'arena:'.$room.':party:'.$party;
        Redis::sadd($namespace, $uuid);
        $this->check_all($namespace, $char);
    }

    public function check_all($namespace, $char)
    {
        $uuids = Redis::smembers($namespace);
        $not_in = $char->teammate()->reject( function ($m) use ($uuids) {
            return in_array($m->uuid, $uuids);
        });
        if ($not_in->count() == 0) {
            GroupArena::dispatch($this->party);
        }
    }

}