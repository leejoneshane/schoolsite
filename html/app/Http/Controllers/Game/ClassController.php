<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GameClass;
use App\Models\GameImage;
use App\Models\GameSkill;
use App\Models\Watchdog;

class ClassController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $professions = GameClass::all();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.classes', ['classes' => $professions]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.class_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $pro = GameClass::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'hp_lvlup' => $request->input('hp_lvlup'),
                'mp_lvlup' => $request->input('mp_lvlup'),
                'ap_lvlup' => $request->input('ap_lvlup'),
                'dp_lvlup' => $request->input('dp_lvlup'),
                'sp_lvlup' => $request->input('sp_lvlup'),
                'base_hp' => $request->input('base_hp'),
                'base_mp' => $request->input('base_mp'),
                'base_ap' => $request->input('base_ap'),
                'base_dp' => $request->input('base_dp'),
                'base_sp' => $request->input('base_sp'),
            ]);
            Watchdog::watch($request, '新增遊戲職業：' . $pro->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.classes')->with('success', '已新增職業：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($class_id)
    {
        $pro = GameClass::find($class_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.class_edit', [ 'pro' => $pro ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $class_id)
    {
        $pro = GameClass::find($class_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $pro->name = $request->input('name');
            $pro->description = $request->input('description');
            $pro->hp_lvlup = $request->input('hp_lvlup');
            $pro->mp_lvlup = $request->input('mp_lvlup');
            $pro->ap_lvlup = $request->input('ap_lvlup');
            $pro->dp_lvlup = $request->input('dp_lvlup');
            $pro->sp_lvlup = $request->input('sp_lvlup');
            $pro->base_hp = $request->input('base_hp');
            $pro->base_mp = $request->input('base_mp');
            $pro->base_ap = $request->input('base_ap');
            $pro->base_dp = $request->input('base_dp');
            $pro->base_sp = $request->input('base_sp');
            $pro->save();
            Watchdog::watch($request, '修改遊戲職業：' . $pro->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.classes')->with('success', '已修改職業：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $class_id)
    {
        $pro = GameClass::find($class_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            Watchdog::watch($request, '刪除遊戲職業：' . $pro->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $pro->delete();
            DB::table('game_classes_images')->where('class_id', $class_id)->delete();
            return redirect()->route('game.classes')->with('success', '已刪除職業：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function gallery($class_id)
    {
        $classes = GameClass::all();
        $pro = GameClass::find($class_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.class_images', [ 'pro' => $pro, 'classes' => $classes ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function store(Request $request, $class_id)
    {
        request()->validate([
            'file' => 'mimes:png,gif|required|max:50000'
        ]);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path(GAME_CHARACTER), $fileName);
            $new = GameImage::create([ 'file_name' => $fileName ]);
            DB::table('game_classes_images')->insert([
                'class_id' => $class_id,
                'image_id' => $new->id,
            ]);
            return response()->json(['success' => $fileName]);
        }
    }

    public function scan($class_id)
    {
        $images = GameImage::forClass($class_id);
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

    public function destroy(Request $request)
    {
        $filename = $request->get('filename');
        $object = GameImage::where('file_name', $filename);
        DB::table('game_classes_images')->where('image_id', $object->id)->delete();
        $path = public_path(GAME_CHARACTER.$object->file_name);
        if (file_exists($path)) unlink($path);
        $path2 = public_path(GAME_FACE.$object->thumbnail);
        if (file_exists($path2)) unlink($path2);
        $object->delete();
        return response()->json(['success' => $filename]);
    }

    public function faces($class_id)
    {
        $classes = GameClass::all();
        $pro = GameClass::find($class_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.class_faces', [ 'pro' => $pro, 'classes' => $classes ]);
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
            $image->move(public_path(GAME_FACE), $fileName);
            $new = GameImage::find($image_id);
            $new->thumbnail = $fileName;
            $new->save();
            $pro = $new->profession;
            return view('game.class_faces', [ 'pro' => $pro ]);
        }
    }

    public function skills($class_id)
    {
        $classes = GameClass::all();
        $pro = GameClass::find($class_id);
        $exclude = $pro->skills->map(function ($sk) {
            return $sk->id;
        })->toArray();
        $skills = GameSkill::all();
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.class_skills', [ 'pro' => $pro, 'classes' => $classes, 'skills' => $skills ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function skills_update(Request $request, $class_id)
    {
        DB::table('game_classes_skills')->where('class_id', $class_id)->delete();
        if (!empty($request->skills)) {
            foreach ($request->input('skills') as $i => $new) {
                DB::table('game_classes_skills')->updateOrInsert([
                    'class_id' => $class_id,
                    'skill_id' => $new,
                ],[
                    'level' => $request->input('level')[$i],
                ]);
            }    
        }
        return redirect()->back()->with('success', '此職業的技能設定已經儲存！');
    }

}
