<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Menu;

class Menus extends Component
{
    public $id;
    
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        $menu = Menu::find($this->id);
        return view('components.menus', ['menu' => $menu]);
    }
}
