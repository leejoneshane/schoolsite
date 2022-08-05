<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Menu;

class Menus extends Component
{
    public $id;
    public $display;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $display)
    {
        $this->id = $id;
        $this->display = $display;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $menu = Menu::find($this->id);
        $items = $menu->childs->sortBy('weight');
        return view('components.menus', ['display' => $this->display, 'menu' => $menu, 'items' => $items]);
    }
}
