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
        $users = [];
        $allResults = [];
        $cursor = 0;
        do {
            list($cursor, $keys) = Redis::scan($cursor, 'match', 'users:*');
            if ($keys) {
                $allResults = array_merge($allResults, $keys);
            }
        } while ($cursor);
        $allResults = array_unique($allResults);
        foreach($allResults as $result){
            $users[] = User::find(Redis::get($result));
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
