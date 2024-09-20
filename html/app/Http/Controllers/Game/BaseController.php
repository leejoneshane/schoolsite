<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\GameBase;
use App\Models\Watchdog;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BaseController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $bases = GameBase::all();
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.bases', ['bases' => $bases]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.base_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameBase::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
                'ap' => $request->input('ap'),
                'dp' => $request->input('dp'),
                'sp' => $request->input('sp'),
            ]);
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_BASE), $fileName);
                $path = public_path(GAME_BASE.$fileName);
                $manager = new ImageManager(new Driver());
                $file = $manager->read($path);
                if ($file->width() > 800) {
                    $file->scale(width: 800);
                    $file->toPng()->save($path);    
                }    
                if ($sk->image_avaliable()) {
                    unlink($sk->image_path());
                }
                $sk->image_file = $fileName;
                $sk->save();
            }
            Watchdog::watch($request, '新增遊戲據點：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.bases')->with('success', '已新增據點：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($base_id)
    {
        $base = GameBase::find($base_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.base_edit', [ 'base' => $base ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $base_id)
    {
        $sk = GameBase::find($base_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk->name = $request->input('name');
            $sk->description = $request->input('description');
            $sk->hp = $request->input('hp');
            $sk->mp = $request->input('mp');
            $sk->ap = $request->input('ap');
            $sk->dp = $request->input('dp');
            $sk->sp = $request->input('sp');
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_BASE), $fileName);
                $path = public_path(GAME_BASE.$fileName);
                $manager = new ImageManager(new Driver());
                $file = $manager->read($path);
                $file->scale(width: 800);
                $file->toPng()->save($path);
                $sk->image_file = $fileName;
            }
            $sk->save();
            Watchdog::watch($request, '修改遊戲據點：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.bases')->with('success', '已修改據點：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $base_id)
    {
        $sk = GameBase::find($base_id);
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            GameParty::where('base_id', $sk->id)->update([
                'base_id' => null,
            ]);
            Watchdog::watch($request, '刪除遊戲據點：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $sk->delete();
            return redirect()->route('game.bases')->with('success', '已刪除據點：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

}
