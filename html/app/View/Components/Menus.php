<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;
use App\Models\Menu;

class Menus extends Component
{
    public $url;
    public $id;
    public $display;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $display)
    {
        $this->url = $id;
        $a = explode('/', $id);
        $this->id = array_pop($a);
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
        return view('components.menus', ['url' => $this->url, 'display' => $this->display, 'menu' => $menu, 'items' => $items]);
    }
}
