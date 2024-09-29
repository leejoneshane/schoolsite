<?php
 
namespace App\Listeners;

use App\Events\EnterArena;
use Illuminate\Support\Facades\Redis;
use App\Events\GamePartyChannel;
 
class LogEnterArena
{

    public function __construct()
    {
        //
    }
 
    public function handle(EnterArena $event)
    {
        $uuid = $event->character->uuid;
        $party = $event->character->party_id;
        $namespace = 'arena-party-'.$party.':'.$uuid;
        $expire = 40 * 60; //40 mintues
        Redis::setex($namespace, $expire, $uuid);
        $in = $this->already('arena-party-'.$party);
        $not_in = $event->character->teammate->reject( function ($m) use ($in) {
            return in_array($m->uuid, $in);
        });
        if ($not_in->count() > 0) {
            broadcast(new GamePartyChannel($event->character->party_id, '請立刻前往競技場集合！'));
        }
    }

    public function already($namespace)
    {
        $prefix = config('database.redis.options.prefix');
        $len = strlen($prefix);
        $uuids = [];
        $allResults = [];
        $cursor = null;
        do {
            list($cursor, $keys) = Redis::scan($cursor, ['match' => $prefix.$namespace.':*']);
            if ($keys) {
                $allResults = array_merge($allResults, $keys);
            }
        } while ($cursor);
        $allResults = array_unique($allResults);
        foreach($allResults as $result){
            $key = substr($result, $len);
            $uuids[] = Redis::get($key);
        }
        return $uuids;
    }

}