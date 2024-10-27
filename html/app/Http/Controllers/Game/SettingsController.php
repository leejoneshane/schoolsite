<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GameSetting;
use App\Models\GameItem;
use App\Models\Watchdog;

class SettingsController extends Controller
{

    public function positive()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $rules = GameSetting::positive($user->uuid);
            return view('game.positive', ['rules' => $rules]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function negative()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $rules = GameSetting::negative($user->uuid);
            return view('game.negative', ['rules' => $rules]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function positive_add()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $items = GameItem::all();
            return view('game.positive_add', [ 'items' => $items]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function negative_add()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            return view('game.negative_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            if ($request->input('type') == 'positive') {
                $sk = GameSetting::create([
                    'uuid' => $user->uuid,
                    'description' => $request->input('description'),
                    'type' => 'positive',
                ]);
            } else {
                $sk = GameSetting::create([
                    'uuid' => $user->uuid,
                    'description' => $request->input('description'),
                    'type' => 'negative',
                ]);
            }
            if ($request->has('xp')) $sk->effect_xp = $request->input('xp');
            if ($request->has('gp')) $sk->effect_gp = $request->input('gp');
            if ($request->has('item')) $sk->effect_item = $request->input('item');
            if ($request->has('hp')) $sk->effect_hp = $request->input('hp');
            if ($request->has('mp')) $sk->effect_mp = $request->input('mp');
            $sk->save();
            Watchdog::watch($request, '新增遊戲教室條款：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($request->input('type') == 'positive') {
                return redirect()->route('game.positive')->with('success', '已新增條款：'.$request->input('description').'！');
            } else {
                return redirect()->route('game.negative')->with('success', '已新增條款：'.$request->input('description').'！');
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($rule_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $rule = GameSetting::find($rule_id);
            if ($rule->type == 'positive') {
                $items = GameItem::all();
                return view('game.positive_edit', [ 'rule' => $rule, 'items' => $items ]);
            } else {
                return view('game.negative_edit', [ 'rule' => $rule ]);
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $rule_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $sk = GameSetting::find($rule_id);
            $sk->uuid = $user->uuid;
            if ($request->has('description')) $sk->description = $request->input('description');
            if ($request->has('xp')) $sk->effect_xp = $request->input('xp');
            if ($request->has('gp')) $sk->effect_gp = $request->input('gp');
            if ($request->has('item')) $sk->effect_item = $request->input('item');
            if ($request->has('hp')) $sk->effect_hp = $request->input('hp');
            if ($request->has('mp')) $sk->effect_mp = $request->input('mp');
            $sk->save();
            Watchdog::watch($request, '修改遊戲教室條款：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($sk->type == 'positive') {
                return redirect()->route('game.positive')->with('success', '已修改條款：'.$request->input('description').'！');
            } else {
                return redirect()->route('game.negative')->with('success', '已修改條款：'.$request->input('description').'！');
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $rule_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $sk = GameSetting::find($rule_id);
            $description = $sk->description;
            Watchdog::watch($request, '刪除遊戲教室條款：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $sk->delete();
            return redirect()->route('game.bases')->with('success', '已刪除條款：'.$description.'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

}
