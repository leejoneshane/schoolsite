<?php
namespace App\Events;

use App\Models\GameParty;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BattleEnd
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GameParty $party1;
    public GameParty $party2;

    public function __construct(GameParty $party1, GameParty $party2)
    {
        $this->party1 = $party1;
        $this->party2 = $party2;
    }

}