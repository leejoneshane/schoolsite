<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\GameFace;

class FaceController extends Controller
{

    public function gallery()
    {
        return view('game.faces');
    }

    public function store(Request $request)
    {
        request()->validate([
            'file' => 'mimes:jpeg,jpg,png,gif|required|max:15000'
        ]);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/faces'), $fileName);
            GameFace::create([ 'file_name' => $fileName ]);
            $path = public_path('images/faces') . $fileName;
            $manager = new ImageManager(new Driver());
            $image = $manager->read($path);
            $image->scale(width: 100);
            $image->save($path);
            return response()->json(['success' => $fileName]);
        }
    }

    public function getImages()
    {
        $images = GameFace::all();
        foreach ($images as $image) {
            $tableImages[] = $image->file_name;
        }
        $data = [];
        $files = scandir(public_path('images/faces'));
        foreach ($files as $file) {
            if ($file !='.' && $file !='..' && in_array($file, $tableImages)) {       
                $obj['name'] = $file;
                $file_path = public_path('images/faces/').$file;
                $obj['size'] = filesize($file_path);
                $obj['path'] = asset('images/faces/'.$file);
                $data[] = $obj;
            }
        }
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $filename = $request->get('filename');
        GameFace::where('file_name', $filename)->delete();
        $path = public_path('images/faces/').$filename;
        if (file_exists($path)) unlink($path);
        return response()->json(['success' => $filename]);
    } 

}
