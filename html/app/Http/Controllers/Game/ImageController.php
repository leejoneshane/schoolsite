<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GameImage;

class ImageController extends Controller
{

    public function gallery()
    {
        return view('game.gallery');
    }

    public function cropImageUploadAjax(Request $request)
    {
        $folderPath = public_path(GAME_CHARACTER);
 
        $image_parts = explode(";base64,", $request->image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
 
        $imageName = uniqid() . '.png';
 
        $imageFullPath = $folderPath.$imageName;
 
        file_put_contents($imageFullPath, $image_base64);
 
         $saveFile = new GameImage;
         $saveFile->file_name = $imageName;
         $saveFile->save();
    
        return response()->json(['success'=>'Crop Image Uploaded Successfully']);
    }

    public function store(Request $request)
    {
        request()->validate([
            'file' => 'mimes:jpeg,jpg,png,gif|required|max:15000'
        ]);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path(GAME_CHARACTER), $fileName);
            GameImage::create([ 'file_name' => $fileName ]);
            $path = public_path(GAME_CHARACTER) . $fileName;
            return response()->json(['success' => $fileName]);
        }
    }

    public function store_thumb(Request $request, $image_id)
    {
        request()->validate([
            'file' => 'mimes:jpeg,jpg,png,gif|required|max:1500'
        ]);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path(GAME_FACE), $fileName);
            GameImage::find($image_id)->update([ 'thumbnail' => $fileName ]);
            $path = public_path(GAME_FACE) . $fileName;
            return response()->json(['success' => $fileName]);
        }
    }

    public function getImages()
    {
        $images = GameImage::all();
        foreach ($images as $image) {
            $tableImages[] = $image->file_name;
        }
        $data = [];
        $files = scandir(public_path(GAME_CHARACTER));
        foreach ($files as $file) {
            if ($file !='.' && $file !='..' && in_array($file, $tableImages)) {       
                $obj['name'] = $file;
                $file_path = public_path(GAME_CHARACTER.$file);
                $obj['size'] = filesize($file_path);
                $obj['path'] = asset(GAME_CHARACTER.$file);
                $data[] = $obj;
            }
        }
        return response()->json($data);
    }

    public function getThumbnails()
    {
        $images = GameImage::all();
        foreach ($images as $image) {
            $tableImages[] = $image->thumbnail;
        }
        $data = [];
        $files = scandir(public_path(GAME_FACE));
        foreach ($files as $file) {
            if ($file !='.' && $file !='..' && in_array($file, $tableImages)) {       
                $obj['name'] = $file;
                $file_path = public_path(GAME_FACE.$file);
                $obj['size'] = filesize($file_path);
                $obj['path'] = asset(GAME_FACE.$file);
                $data[] = $obj;
            }
        }
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $filename = $request->get('filename');
        $object = GameImage::where('file_name', $filename);
        $path = public_path(GAME_CHARACTER.$object->file_name);
        if (file_exists($path)) unlink($path);
        $path2 = public_path(GAME_FACE.$object->thumbnail);
        if (file_exists($path2)) unlink($path2);
        $object->delete();
        return response()->json(['success' => $filename]);
    }

}
