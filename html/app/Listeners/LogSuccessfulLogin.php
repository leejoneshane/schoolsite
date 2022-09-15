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
        $id = $event->user->id;
        $namespace = 'online-users:'.$id;
        $expire = config('session.lifetime') * 60;
        Redis::setex($namespace, $expire, $id);
    }
}