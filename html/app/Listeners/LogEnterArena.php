<?php
 
namespace App\Listeners;

use Illuminate\Support\Facades\Redis;
use App\Events\EnterArena;
use App\Events\GroupArena;
use App\Models\GameParty;
 
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
        $this->check_all($party);
    }

    public function check_all($party)
    {
        $prefix = config('database.redis.options.prefix');
        $len = strlen($prefix);
        $uuids = [];
        $allResults = [];
        $cursor = null;
        $namespace = 'arena-party-'.$party;
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
        $party_obj = GameParty::find($party);
        $not_in = $party_obj->members->reject( function ($m) use ($uuids) {
            return in_array($m->uuid, $uuids);
        });
        if ($not_in->count() == 0) {
            GroupArena::dispatch($party_obj);
        }
    }

}