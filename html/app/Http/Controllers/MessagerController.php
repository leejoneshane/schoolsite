<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\PrivateMessage;
use App\Events\PublicMessage;

class MessagerController extends Controller
{

    public function send(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $message = $request->input('message');
        broadcast(new PrivateMessage($from, $to, $message));
    }

    public function broadcast(Request $request)
    {
        $message = $request->input('message');
        broadcast(new PublicMessage($message));
    }

    public function list()
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
            $users[] = User::find(Redis::get($key));
        }
        return response()->json($users);
    }

}
