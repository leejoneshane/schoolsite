<?php
namespace App\Events;

use App\Models\GameCharacter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExitArena
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GameCharacter $character;

    public function __construct(GameCharacter $character)
    {
        $this->character = $character;
    }

}