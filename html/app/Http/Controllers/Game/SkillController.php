<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GameClass;
use App\Models\GameSkill;
use App\Models\GameMonster;
use App\Models\Watchdog;

class SkillController extends Controller
{

    public function index($type = null, $id = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            if (!$type || $id == 'all') {
                $skills = GameSkill::all()->sortBy('object');
            } elseif ($type == 'class') {
                $skills = GameSkill::forClass($id);
            } elseif ($type == 'monster') {
                $skills = GameSkill::forMonster($id);
            }
            $classes = GameClass::all();
            $monsters = GameMonster::all();
            return view('game.skills', ['type' => $type, 'id' => $id, 'monsters' => $monsters, 'classes' => $classes, 'skills' => $skills]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function add(Request $request)
    {
        $referer = $request->headers->get('referer');
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            return view('game.skill_add', [ 'referer' => $referer ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameSkill::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'passive' => ($request->input('passive') == 'yes'),
                'object' => $request->input('object'),
                'hit_rate' => $request->input('hit_rate'),
                'cost_mp' => $request->input('cost_mp'),
                'ap' => $request->input('ap'),
                'steal_hp' => $request->input('steal_hp'),
                'steal_mp' => $request->input('steal_mp'),
                'steal_gp' => $request->input('steal_gp'),
                'effect_hp' => $request->input('effect_hp'),
                'effect_mp' => $request->input('effect_mp'),
                'effect_ap' => $request->input('effect_ap'),
                'effect_dp' => $request->input('effect_dp'),
                'effect_sp' => $request->input('effect_sp'),
                'effect_times' => $request->input('effect_times'),
                'status' => $request->input('status'),
                'inspire' => $request->input('inspire'),
                'earn_xp' => $request->input('earn_xp'),
                'earn_gp' => $request->input('earn_gp'),
            ]);
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_SKILL), $fileName);
                $sk->gif_file = $fileName;
                $sk->save();
            }
            Watchdog::watch($request, '新增遊戲技能：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->to($request->input('referer'))->with('success', '已新增技能：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit(Request $request, $skill_id)
    {
        $referer = $request->headers->get('referer');
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $skill = GameSkill::find($skill_id);
            return view('game.skill_edit', [ 'skill' => $skill, 'referer' => $referer ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $skill_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameSkill::find($skill_id);
            $sk->name = $request->input('name');
            $sk->description = $request->input('description');
            $sk->passive = ($request->input('passive') == 'yes');
            $sk->object = $request->input('object');
            $sk->hit_rate = $request->input('hit_rate');
            $sk->cost_mp = $request->input('cost_mp');
            $sk->ap = $request->input('ap');
            $sk->steal_hp = $request->input('steal_hp');
            $sk->steal_mp = $request->input('steal_mp');
            $sk->steal_gp = $request->input('steal_gp');
            $sk->effect_hp = $request->input('effect_hp');
            $sk->effect_mp = $request->input('effect_mp');
            $sk->effect_ap = $request->input('effect_ap');
            $sk->effect_dp = $request->input('effect_dp');
            $sk->effect_sp = $request->input('effect_sp');
            $sk->effect_times = $request->input('effect_times');
            $sk->status = $request->input('status');
            $sk->inspire = $request->input('inspire');
            $sk->earn_xp = $request->input('earn_xp');
            $sk->earn_gp = $request->input('earn_gp');
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = Str::ulid()->toBase32() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path(GAME_SKILL), $fileName);
                if ($sk->image_avaliable()) {
                    unlink($sk->image_path());
                }
                $sk->gif_file = $fileName;
            }
            $sk->save();
            Watchdog::watch($request, '修改遊戲技能：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->to($request->input('referer'))->with('success', '已修改技能：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $skill_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameSkill::find($skill_id);
            Watchdog::watch($request, '刪除遊戲技能：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($sk->image_avaliable()) {
                unlink($sk->image_path());
            }
            $sk->delete();
            DB::table('game_classes_skills')->where('skill_id', $skill_id)->delete();
            return redirect()->route('game.skills')->with('success', '已刪除技能：'.$request->input('name').'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

}
