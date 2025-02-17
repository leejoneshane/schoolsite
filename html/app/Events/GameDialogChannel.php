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
use App\Models\GameCharacter;
use App\Models\GameParty;

class GameDialogChannel implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GameCharacter $from;
    public GameCharacter $to;
    public GameParty $from_party;
    public GameParty $to_party;
    public string $message;
    public string $code; //game service code

    public function __construct($from, $to, $code = '', $message = '')
    {
        $this->from = GameCharacter::find($from);
        $this->to = GameCharacter::find($to);
        $this->from_party = $this->from->party;
        $this->to_party = $this->to->party;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() : Channel
    {
        $student = $this->to->student;
        return new PrivateChannel('dialog.' . $student->stdno);
    }
}