<?php
 
namespace App\Listeners;
 
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Redis;
 
class LogSuccessfulLogout
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
    public function handle(Logout $event)
    {
        $id = $event->user->id;
        $namespace = 'users:'.$id;

        // Deleting user from redis database when they log out
        Redis::del($namespace);
    }
}