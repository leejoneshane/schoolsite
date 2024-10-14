<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Seats;
use App\Models\GameConfigure;
use App\Models\GameParty;
use App\Models\GameCharacter;
use App\Models\GameClass;
use App\Models\GameBase;
use App\Models\Watchdog;

class ClassroomController extends Controller
{

    public function config()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $room = Classroom::find(session('gameclass'));
            $config = GameConfigure::find($room->id);
            if (!$config) {
                $config = GameConfigure::create([
                    'classroom_id' => $room->id,
                ]);
            }
            return view('game.classroom_config', [ 'room' => $room, 'config' => $config]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function save_config(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $sk = GameConfigure::find(session('gameclass'));
            $sk->daily_mp = $request->input('mp');
            if ($request->input('change_base') == 'yes') {
                $sk->change_base = true;
            } else {
                $sk->change_base = false;
            }
            if ($request->input('change_class') == 'yes') {
                $sk->change_class = true;
            } else {
                $sk->change_class = false;
            }
            if ($request->input('arena_open') == 'yes') {
                $sk->arena_open = true;
            } else {
                $sk->arena_open = false;
            }
            if ($request->input('furniture_shop') == 'yes') {
                $sk->furniture_shop = true;
            } else {
                $sk->furniture_shop = false;
            }
            if ($request->input('item_shop') == 'yes') {
                $sk->item_shop = true;
            } else {
                $sk->item_shop = false;
            }
            if ($request->input('pet_shop') == 'yes') {
                $sk->pet_shop = true;
            } else {
                $sk->pet_shop = false;
            }
            $sk->save();
            Watchdog::watch($request, '修改遊戲規則：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->back()->with('success', '已修改遊戲規則！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function regroup()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $room = Classroom::find(session('gameclass'));
            $parties = GameParty::findByClass($room->id);
            $partyless = GameCharacter::findNoParty($room->id);    
            return view('game.classroom_regroup', [ 'room' => $room, 'parties' => $parties, 'partyless' => $partyless]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function change_group(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        if ($request->input('party') == 0) {
            $character->party_id = null;
        } else {
            $character->party_id = $request->input('party');
        }
        $character->save();
        return response()->json(['success' => $character->party_id]);
    }

    public function party_add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $group_no = GameParty::findByClass(session('gameclass'))->count();
            $group_no ++;
            $bases = GameBase::all();
            return view('game.party_add', [ 'group_no' => $group_no, 'bases' => $bases, 'url' => url()->previous()]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function party_insert(Request $request)
    {

        $party = GameParty::create([
            'classroom_id' => session('gameclass'),
            'group_no' => $request->input('group_no'),
            'name' => $request->input('name'),
        ]);
        if (!empty($request->input('description'))) {
            $party->description = $request->input('description');
        }
        if ($request->input('base') == 0) {
            $party->remove_fundation();
        } else {
            $party->change_fundation($request->input('base'));
        }
        return redirect()->to($request->input('url'));
    }

    public function party_edit($party_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $party = GameParty::find($party_id);
            $bases = GameBase::all();
            return view('game.party_edit', [ 'party' => $party, 'bases' => $bases, 'url' => url()->previous()]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function party_update(Request $request, $party_id)
    {
        $party = GameParty::find($party_id);
        $party->group_no = $request->input('group_no');
        $party->name = $request->input('name');
        $party->description = $request->input('description');
        $party->uuid = $request->input('leader');
        if ($request->input('base') == 0) {
            $party->remove_fundation();
        } else {
            $party->change_fundation($request->input('base'));
        }
        return redirect()->to($request->input('url'));
    }

    public function party_remove(Request $request, $party_id)
    {
        $party = GameParty::find($party_id);
        foreach ($party->withAbsent as $char) {
            $char->party_id = null;
            $char->save();
        }
        $party->delete();
        return redirect()->back();
    }

    public function characters()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $room = Classroom::find(session('gameclass'));
            $parties = GameParty::findByClass($room->id);
            $characters = GameCharacter::findByClass($room->id);
            return view('game.characters', ['room' => $room, 'parties' => $parties, 'characters' => $characters]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function character_edit($uuid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $character = GameCharacter::find($uuid);
            $classes = GameClass::all();
            return view('game.profession_setup', [ 'action' => route('game.profession_setup', [ 'uuid' => $character->uuid ]), 'character' => $character, 'classes' => $classes]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function character_class(Request $request, $uuid)
    {
        $pro = GameClass::find($request->input('class_id'));
        $character = GameCharacter::find($uuid);
        $character->change_class($pro->id);
        return view('game.image_setup', [ 'action' => route('game.image_setup', [ 'uuid' => $character->uuid ]), 'character' => $character ]);
    }

    public function image_edit(Request $request, $uuid)
    {
        $character = GameCharacter::find($uuid);
        return view('game.image_setup', [ 'action' => route('game.image_setup', [ 'uuid' => $character->uuid ]), 'character' => $character ]);
    }

    public function character_image(Request $request, $uuid)
    {
        $character = GameCharacter::find($uuid);
        $character->image_id = $request->input('image_id');
        $character->save();
        return redirect()->route('game.characters');
    }

    public function dungeons()
    {
        $uuid = Auth::user()->uuid;
        $dungeons = GameDungeon::findByTeacher($uuid);
        return view('game.dungeons', [ 'dungeons' => $dungeons ]);
    }

    public function reset()
    {
        $room = Classroom::find(session('gameclass'));
        return view('game.classroom_reset', [ 'room' => $room ]);
    }

    public function do_reset(Request $request)
    {
        $room_id = session('gameclass');
        $myclass = Classroom::find($room_id);
        $students = $myclass->students;
        foreach ($students as $stu) {
            $character = GameCharacter::find($stu->uuid);
            if (!$character) {
                GameCharacter::create([
                    'uuid' => $stu->uuid,
                    'classroom_id' => $room_id,
                    'party_id' => null,
                    'seat' => $stu->seat,
                    'name' => $stu->realname,
                ]);
            }
        }
        if ($request->input('party') == 'yes') {
            GameParty::where('classroom_id', $room_id)->delete();
            $seats = Seats::where('uuid', $request->user()->uuid)->where('class_id', $room_id)->first();
            if (!$seats) $seats = Seats::findByClass($room_id)->first();
            if ($seats) {
                $grouped = $seats->students->groupBy(function ($stu) {
                    return $stu->pivot->group_no;
                });
                foreach ($grouped as $gno => $students) {
                    $party = GameParty::create([
                        'classroom_id' => $room_id,
                        'group_no' => $gno,
                        'name' => '第'.$gno.'組',
                    ]);
                    foreach ($students as $stu) {
                        GameCharacter::updateOrCreate([
                            'uuid' => $stu->uuid,
                        ],[
                            'classroom_id' => $room_id,
                            'party_id' => $party->id,
                            'seat' => $stu->seat,
                            'name' => $stu->realname,
                        ]);
                    }
                }
                $parties = GameParty::findByClass($room_id);
            }
        } else {
            if ($request->input('base') == 'yes') {
                $parties = GameParty::findByClass($room_id);
                foreach ($parties as $p) {
                    if ($p->fundation) {
                        $p->effect_hp -= $p->fundation->hp;
                        $p->effect_mp -= $p->fundation->mp;
                        $p->effect_ap -= $p->fundation->ap;
                        $p->effect_dp -= $p->fundation->dp;
                        $p->effect_sp -= $p->fundation->sp;
                    }
                    $p->base_id = null;
                    $p->save();
                }
            }
            if ($request->input('furniture') == 'yes') {
                $parties = GameParty::findByClass($room_id);
                foreach ($parties as $p) {
                    DB::table('game_parties_furnitures')->where('party_id', $p->id)->delete();
                    if ($p->fundation) {
                        $p->effect_hp = $p->fundation->hp;
                        $p->effect_mp = $p->fundation->mp;
                        $p->effect_ap = $p->fundation->ap;
                        $p->effect_dp = $p->fundation->dp;
                        $p->effect_sp = $p->fundation->sp;
                    } else {
                        $p->effect_hp = 0;
                        $p->effect_mp = 0;
                        $p->effect_ap = 0;
                        $p->effect_dp = 0;
                        $p->effect_sp = 0;
                    }
                    $p->save();
                }
            }
            if ($request->input('treasury') == 'yes') {
                GameParty::where('classroom_id', $room_id)->update([
                    'treasury' => 0,
                ]);
            }
            if ($request->input('pick1') == 'yes') {
                GameParty::where('classroom_id', $room_id)->update([
                    'pick_up' => 0,
                ]);
            }
        }
        if ($request->input('character') == 'yes') {
            foreach ($students as $stu) {
                $character = GameCharacter::find($stu->uuid);
                $character->title = null;
                $character->class_id = null;
                $character->image_id = null;
                $character->level = 1;
                $character->xp = 0;
                $character->max_hp = 0;
                $character->hp = 0;
                $character->max_mp = 0;
                $character->mp = 0;
                $character->ap = 0;
                $character->dp = 0;
                $character->sp = 0;
                $character->gp = 0;
                $character->temp_effect = null;
                $character->effect_value = 0;
                $character->effect_timeout = null;
                $character->buff = null;
                $character->absent = 0;
                $character->pick_up = 0;
                $character->save();
                $character->force_levelup(1);
            }
        } else {
            if ($request->input('profession') == 'yes') {
                foreach ($students as $stu) {
                    $character = GameCharacter::find($stu->uuid);
                    $character->class_id = null;
                    $character->image_id = null;
                    $character->save();
                }
            }
            if ($request->input('level') == 'yes') {
                foreach ($students as $stu) {
                    $character = GameCharacter::find($stu->uuid);
                    $character->force_levelup($request->input('levelup'));
                }
            }
            if ($request->input('item') == 'yes') {
                foreach ($students as $stu) {
                    DB::table('game_characters_items')->where('uuid', $stu->uuid)->delete();
                }
            }
            if ($request->input('gold') == 'yes') {
                foreach ($students as $stu) {
                    $character = GameCharacter::find($stu->uuid);
                    $character->gp = 0;
                    $character->save();
                }
            }
            if ($request->input('point') == 'yes') {
                foreach ($students as $stu) {
                    $character = GameCharacter::find($stu->uuid);
                    $character->hp = $character->max_hp;
                    $character->mp = $character->max_mp;
                    $character->temp_effect = null;
                    $character->effect_value = 0;
                    $character->effect_timeout = null;
                    $character->buff = null;
                    $character->save();
                }
            }
            if ($request->input('pick2') == 'yes') {
                foreach ($students as $stu) {
                    $character = GameCharacter::find($stu->uuid);
                    $character->pick_up = 0;
                    $character->save();
                }
            }
        }
        return redirect()->route('game');
    }

}