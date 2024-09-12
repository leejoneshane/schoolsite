<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Classroom;

class GameNav extends Component
{
    public $room_id;
    
    public function __construct($id)
    {
        $this->room_id = $id;
    }

    public function render()
    {
        $room = Classroom::find($this->room_id);
        return view('components.navigation', ['room' => $room]);
    }
}
