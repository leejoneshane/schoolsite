<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WritingGenre;
use App\Models\WritingContext;

class WritingController extends Controller
{

    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->is_admin || $user->hasPermission('club.manager');
        $genres = WritingGenre::all();
        $genre = WritingGenre::first();
        if ($request->input('order')) {
            $order = $request->input('order');
        } else {
            $order = 'updated_at';
        }
        if ($genre) {
            $contexts = $genre->contexts()->orderByDesc($order)->paginate(16);
        } else {
            $contexts = collect();
        }
        return view('app.writing', ['manager' => $manager, 'genres' => $genres, 'genre' => $genre, 'order' => $order, 'contexts' => $contexts]);
    }

    public function add($genre)
    {

    }

    public function insert(Request $request, $genre)
    {

    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function remove($id)
    {

    }

    public function rank(Request $request, $section = null)
    {

    }

}
