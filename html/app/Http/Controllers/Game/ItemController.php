<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\GameItem;
use App\Models\Watchdog;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ItemController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $items = GameItem::all();
            return view('game.items', ['items' => $items]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add()
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.item_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameItem::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'passive' => ($request->input('passive') == 'yes'),
                'object' => $request->input('object'),
                'hit_rate' => $request->input('hit_rate'),
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
                'ap' => $request->input('ap'),
                'dp' => $request->input('dp'),
                'sp' => $request->input('sp'),
                'effect_times' => $request->input('effect_times'),
                'status' => $request->input('status'),
                'inspire' => $request->input('inspire'),
                'gp' => $request->input('gp'),
            ]);
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_ITEM), $fileName);
                $path = public_path(GAME_ITEM.$fileName);
                $manager = new ImageManager(new Driver());
                $file = $manager->read($path);
                $file->scale(width: 300);
                $file->toPng()->save($path);
                $sk->image_file = $fileName;
                $sk->save();
            }
            Watchdog::watch($request, '新增遊戲道具：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.items')->with('success', '已新增道具：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($item_id)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $item = GameItem::find($item_id);
            return view('game.item_edit', [ 'item' => $item ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $item_id)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameItem::find($item_id);
            $sk->name = $request->input('name');
            $sk->description = $request->input('description');
            $sk->passive = ($request->input('passive') == 'yes');
            $sk->object = $request->input('object');
            $sk->hit_rate = $request->input('hit_rate');
            $sk->hp = $request->input('hp');
            $sk->mp = $request->input('mp');
            $sk->ap = $request->input('ap');
            $sk->dp = $request->input('dp');
            $sk->sp = $request->input('sp');
            $sk->effect_times = $request->input('effect_times');
            $sk->status = $request->input('status');
            $sk->inspire = $request->input('inspire');
            $sk->gp = $request->input('gp');
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_ITEM), $fileName);
                $path = public_path(GAME_ITEM.$fileName);
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
            Watchdog::watch($request, '修改遊戲道具：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.items')->with('success', '已修改道具：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $item_id)
    {
        $user = Auth::user();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameItem::find($item_id);
            Watchdog::watch($request, '刪除遊戲道具：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($sk->image_avaliable()) {
                unlink($sk->image_path());
            }
            $sk->delete();
            return redirect()->route('game.items')->with('success', '已刪除道具：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

}
