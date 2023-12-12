<?php
 
namespace App\Listeners;
 
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Redis;
 
class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
 
    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user->user_type == 'Teacher') {
            $id = $event->user->id;
            $namespace = 'online-users:'.$id;
            $expire = config('session.lifetime') * 7200;
            Redis::setex($namespace, $expire, $id);
        }
    }
}