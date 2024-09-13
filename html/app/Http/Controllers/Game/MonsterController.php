<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GameMonster;
use App\Models\GameImage;
use App\Models\Watchdog;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MonsterController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $monsters = GameMonster::all();
            return view('game.monsters', ['monsters' => $monsters]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.monster_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $m = GameMonster::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'hit_rate' => $request->input('hit_rate'),
                'crit_rate' => $request->input('crit_rate'),
                'max_hp' => $request->input('hp'),
                'hp' => $request->input('hp'),
                'ap' => $request->input('ap'),
                'dp' => $request->input('dp'),
                'sp' => $request->input('sp'),
            ]);
            Watchdog::watch($request, '新增遊戲怪物：' . $m->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.monsters')->with('success', '已新增怪物：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($monster_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $pro = GameMonster::find($monster_id);
            return view('game.monster_edit', [ 'monster' => $pro ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $monster_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $pro = GameMonster::find($monster_id);
            $pro->name = $request->input('name');
            $pro->description = $request->input('description');
            $pro->hit_rate = $request->input('hit_rate');
            $pro->crit_rate = $request->input('crit_rate');
            $pro->max_hp = $request->input('hp');
            $pro->hp = $request->input('hp');
            $pro->ap = $request->input('ap');
            $pro->dp = $request->input('dp');
            $pro->sp = $request->input('sp');
            $pro->save();
            Watchdog::watch($request, '修改遊戲怪物：' . $pro->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.monsters')->with('success', '已修改怪物：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $monster_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $pro = GameMonster::find($monster_id);
            Watchdog::watch($request, '刪除遊戲怪物：' . $pro->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            foreach ($pro->images as $image) {
                $image->delete();
            }
            $name = $pro->name;
            $pro->delete();
            DB::table('game_monsters_images')->where('monster_id', $monster_id)->delete();
            return redirect()->route('game.monsters')->with('success', '已刪除怪物：'.$name.'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function gallery($monster_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $monsters = GameMonster::all();
            $pro = GameMonster::find($monster_id);
            return view('game.monster_images', [ 'monster' => $pro, 'monsters' => $monsters ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function store(Request $request, $monster_id)
    {
        request()->validate([
            'file' => 'mimes:png,gif|required|max:50000'
        ]);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path(GAME_MONSTER), $fileName);
            $path = public_path(GAME_MONSTER.$fileName);
            $manager = new ImageManager(new Driver());
            $file = $manager->read($path);
            if ($file->width() > 300) {
                $file->scale(width: 300);
                $file->toPng()->save($path);    
            }
            $new = GameImage::create([ 'file_name' => $fileName ]);
            DB::table('game_monsters_images')->insert([
                'monster_id' => $monster_id,
                'image_id' => $new->id,
            ]);
            return response()->json(['success' => $fileName]);
        }
    }

    public function scan($monster_id)
    {
        $images = GameImage::forMonster($monster_id);
        foreach ($images as $image) {
            $tableImages[] = $image->file_name;
        }
        $data = [];
        $files = scandir(public_path(GAME_MONSTER));
        foreach ($files as $file) {
            if ($file !='.' && $file !='..' && in_array($file, $tableImages)) {
                $obj['name'] = $file;
                $file_path = public_path(GAME_MONSTER.$file);
                $obj['size'] = filesize($file_path);
                $obj['path'] = asset(GAME_MONSTER.$file);
                $data[] = $obj;
            }
        }
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $filename = $request->get('filename');
        $object = GameImage::where('file_name', $filename)->first();
        DB::table('game_monsters_images')->where('image_id', $object->id)->delete();
        $path = public_path(GAME_MONSTER.$object->file_name);
        if (file_exists($path)) unlink($path);
        if ($object->thumbnail) {
            $path2 = public_path(GAME_MONSTER.$object->thumbnail);
            if (file_exists($path2)) unlink($path2);    
        }
        $object->delete();
        return response()->json(['success' => $filename]);
    }

    public function faces($monster_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $monsters = GameMonster::all();
            $pro = GameMonster::find($monster_id);
            return view('game.monster_faces', [ 'monster' => $pro, 'monsters' => $monsters ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function face_update(Request $request, $image_id)
    {
        request()->validate([
            'face' => 'mimes:png|required|max:15000'
        ]);
        if ($request->hasFile('face')) {
            $image = $request->file('face');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path(GAME_MONSTER), $fileName);
            $path = public_path(GAME_MONSTER.$fileName);
            $manager = new ImageManager(new Driver());
            $file = $manager->read($path);
            if ($file->width() > 80) {
                $file->resize(80, 80);
                $file->toPng()->save($path);    
            }
            $file->toPng()->save($path);
            $new = GameImage::find($image_id);
            if ($new->thumb_avaliable()) {
                unlink($new->thumb_path());
            }
            $new->thumbnail = $fileName;
            $new->save();
            $pro = $new->profession;
            return view('game.monster_faces', [ 'pro' => $pro ]);
        }
    }

}
