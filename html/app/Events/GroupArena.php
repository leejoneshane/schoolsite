<?php
namespace App\Events;

use App\Models\GameParty;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupArena
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GameParty $party;

    public function __construct(GameParty $party)
    {
        $this->party = $party;
    }

}