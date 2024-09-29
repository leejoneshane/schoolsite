<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Redis;

class LogSuccessfulLogout
{

    public function __construct()
    {
        //
    }

    public function handle(Logout $event)
    {
        if ($event->user->user_type == 'Teacher') {
            $id = $event->user->id;
            $namespace = 'online-users:'.$id;
            Redis::del($namespace);
        }
    }
}