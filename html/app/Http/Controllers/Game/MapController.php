<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GameMap;
use App\Models\Watchdog;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MapController extends Controller
{

    public function index()
    {
        //
    }

    public function gallery()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $maps = GameMap::all();
            return view('game.maps', [ 'maps' => $maps ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function store(Request $request)
    {
        request()->validate([
            'file' => 'mimes:png,gif|required|max:50000'
        ]);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path(GAME_MAP), $fileName);
            $path = public_path(GAME_MAP.$fileName);
            $manager = new ImageManager(new Driver());
            $file = $manager->read($path);
            if ($file->width() > 2048) {
                $file->scale(width: 2048);
                $file->toPng()->save($path);
            }
            GameMap::create([ 
                'map' => GAME_MAP.$fileName,
            ]);
            return response()->json(['success' => $fileName]);
        }
    }

    public function scan()
    {
        $tableImages = [];
        $images = GameMap::all();
        foreach ($images as $image) {
            $tableImages[] = basename($image->map);
        }
        $data = [];
        $files = scandir(public_path(GAME_MAP));
        foreach ($files as $file) {
            if ($file !='.' && $file !='..' && in_array($file, $tableImages)) {
                $obj['name'] = $file;
                $file_path = public_path(GAME_MAP.$file);
                $obj['size'] = filesize($file_path);
                $obj['path'] = asset(GAME_MAP.$file);
                $data[] = $obj;
            }
        }
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $filename = $request->get('filename');
        $object = GameMap::where('map', 'like', '%'.$filename)->first();
        if ($object) {
            $path = public_path($object->map);
            if (file_exists($path)) unlink($path);
            $object->delete();    
        }
        return response()->json(['success' => $filename]);
    }

}
