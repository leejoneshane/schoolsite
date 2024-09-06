<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

class GameController extends Controller
{

    public function index()
    {
        return view('game.index');
    }

}
