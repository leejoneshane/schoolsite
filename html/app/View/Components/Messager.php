<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class Messager extends Component
{
    public static function loggedUsers()
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
        return $users;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $users = self::loggedUsers();
        return view('components.messager', ['users' => $users]);
    }
}
