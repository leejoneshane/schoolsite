<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Intervention\Image\Image;
use App\Models\GameFace;

class AdminController extends Controller
{

    public function facees()
    {
        $faces = GameFace::all();
        return view('game.faces', [ 'faces' => $faces ]);
    }

    public function store(Request $request)
    {
        request()->validate([
            'file' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
        ]);

        if ($request->hasFile('file')) {
            $face = $request->file('file');
            $filename = time() . '_' . $face->getClientOriginalName();
            $face->move(public_path('game/faces'), $filename);
            $image = Image::make(public_path("game/faces/$filename"))->resize(100, 100);
            $image->save(public_path("game/faces/$filename"));
        }
        return redirect()->route('game.faces');
    }

}
