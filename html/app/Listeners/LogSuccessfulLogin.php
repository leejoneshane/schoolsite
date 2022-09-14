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
        $namespace = 'users:'.$id;

        // Getting the expiration from the session config file. Converting from minutes to seconds.
        $expire = config('session.lifetime') * 60;

        // Setting redis using id as value
        Redis::set($namespace, $id);
        Redis::expire($namespace, $expire);
    }
}