<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Menu;

class Menus extends Component
{
    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $menu = Menu::find($this->id);
        return view('components.menus', ['menu' => $menu]);
    }
}
