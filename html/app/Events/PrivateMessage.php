<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PrivateMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $from;
    public int $to;
    public string $from_user;
    public string $to_user;
    public string $message;

    public function __construct($from, $to, $message)
    {
        $this->from = $from;
        $this->to = $to;
        $this->from_user = User::find($from)->profile->realname;
        $this->to_user = User::find($to)->profile->realname;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() : Channel
    {
        return new PrivateChannel('private.' . $this->to);
    }
}