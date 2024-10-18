<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Redis;

class LogSuccessfulLogin
{

    public function __construct()
    {
        //
    }

    public function handle(Login $event)
    {
        if ($event->user->user_type == 'Teacher') {
            $id = $event->user->id;
            $namespace = 'online-users:'.$id;
            $expire = config('session.lifetime') * 60;
            Redis::setex($namespace, $expire, $id);
        } else {
            $uuid = $event->user->uuid;
            $namespace = 'online-character:'.$uuid;
            $expire = 40 * 60;
            Redis::setex($namespace, $expire, $uuid);
        }
    }
}