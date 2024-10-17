<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\GameFurniture;
use App\Models\Watchdog;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FurnitureController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $furnitures = GameFurniture::all()->sortBy('gp');
            return view('game.furnitures', ['furnitures' => $furnitures]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add()
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.furniture_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameFurniture::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
                'ap' => $request->input('ap'),
                'dp' => $request->input('dp'),
                'sp' => $request->input('sp'),
                'gp' => $request->input('gp'),
            ]);
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_FURNITURE), $fileName);
                $path = public_path(GAME_FURNITURE.$fileName);
                $manager = new ImageManager(new Driver());
                $file = $manager->read($path);
                $file->scale(width: 300);
                $file->toPng()->save($path);
                $sk->image_file = $fileName;
                $sk->save();
            }
            Watchdog::watch($request, '新增遊戲家具：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.furnitures')->with('success', '已新增家具：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($base_id)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $furniture = GameFurniture::find($base_id);
            return view('game.furniture_edit', [ 'furniture' => $furniture ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $furniture_id)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameFurniture::find($furniture_id);
            $sk->name = $request->input('name');
            $sk->description = $request->input('description');
            $sk->hp = $request->input('hp');
            $sk->mp = $request->input('mp');
            $sk->ap = $request->input('ap');
            $sk->dp = $request->input('dp');
            $sk->sp = $request->input('sp');
            $sk->gp = $request->input('gp');
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_FURNITURE), $fileName);
                $path = public_path(GAME_FURNITURE.$fileName);
                $manager = new ImageManager(new Driver());
                $file = $manager->read($path);
                if ($file->width() > 300) {
                    $file->scale(width: 300);
                    $file->toPng()->save($path);    
                }
                if ($sk->image_avaliable()) {
                    unlink($sk->image_path());
                }
                $sk->image_file = $fileName;
            }
            $sk->save();
            Watchdog::watch($request, '修改遊戲家具：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.furnitures')->with('success', '已修改家具：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $furniture_id)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameFurniture::find($furniture_id);
            Watchdog::watch($request, '刪除遊戲家具：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($sk->image_avaliable()) {
                unlink($sk->image_path());
            }
            $sk->delete();
            return redirect()->route('game.furnitures')->with('success', '已刪除家具：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

}
