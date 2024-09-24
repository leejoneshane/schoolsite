<?php

namespace App\Http\Controllers\Game;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\GameCharacterChannel;
use App\Events\GamePartyChannel;
use App\Events\GameRoomChannel;

class MessagerController extends Controller
{

    public function personal(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $message = $request->input('message');
        broadcast(new GameCharacterChannel($from, $to, $message));
    }

    public function party(Request $request)
    {
        $party_id = $request->input('party');
        $message = $request->input('message');
        broadcast(new GamePartyChannel($party_id, $message));
    }

    public function classroom(Request $request)
    {
        $room_id = $request->input('room');
        $message = $request->input('message');
        broadcast(new GameRoomChannel($room_id, $message));
    }

    public function list($room_id)
    {
        $prefix = config('database.redis.options.prefix');
        $len = strlen($prefix);
        $users = [];
        $allResults = [];
        $cursor = null;
        do {
            list($cursor, $keys) = Redis::scan($cursor, ['match' => $prefix.'online-users:*']);
            if ($keys) {
                $allResults = array_merge($allResults, $keys);
            }
        } while ($cursor);
        $allResults = array_unique($allResults);
        foreach($allResults as $result){
            $key = substr($result, $len);
            $user = User::find(Redis::get($key));
            if ($user->profile->class_id == $room_id) {
                $users[] = $user->profile;
            }
        }
        return response()->json($users);
    }

}
