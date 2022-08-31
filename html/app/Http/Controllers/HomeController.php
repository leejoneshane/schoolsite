<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!Menu::find('calendar')) {
            Menu::create([
                'id' => 'calendar',
                'parent_id' => 'main',
                'caption' => '學校行事曆',
                'url' => 'route.calendar',
                'weight' => 10,
            ]);
        }
    }

    public function index()
    {
        return view('home');
    }

}
