<?php
namespace App\Events;

use App\Models\GameParty;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BattleAction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GameParty $party;
    public string $message;

    public function __construct(GameParty $party, $message)
    {
        $this->party = $party;
        $this->message = $message;
    }

}