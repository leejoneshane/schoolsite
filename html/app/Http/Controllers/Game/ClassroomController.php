<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Classroom;
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

    function regroup()
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

    function change_group(Request $request)
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

    function party_edit($party_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $party = GameParty::find($party_id);
            $bases = GameBase::all();
            return view('game.party_edit', [ 'party' => $party, 'bases' => $bases]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    function party_update(Request $request, $party_id)
    {
        $party = GameParty::find($party_id);
        $party->name = $request->input('name');
        $party->description = $request->input('description');
        $party->base_id = $request->input('base');
        $party->save();
        return redirect()->back();
    }

    function characters()
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

    function character_edit($uuid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('game.manager');
        if ($user->is_admin || $manager) {
            $character = GameCharacter::find($uuid);
            $classes = GameClass::all();
            return view('game.profession_setup', [ 'character' => $character, 'classes' => $classes]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    function character_update(Request $request, $uuid)
    {
        $character = GameCharacter::find($uuid);
        $character->class_id = $request->input('class_id');
        $character->image_id = $request->input('image_id');
        $character->save();
        return redirect()->route('game.characters');
    }

}
