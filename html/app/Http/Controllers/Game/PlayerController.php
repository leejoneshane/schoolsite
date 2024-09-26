<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Seats;
use App\Models\GameSence;
use App\Models\GameParty;
use App\Models\GameCharacter;
use App\Models\GameClass;
use App\Models\GameSkill;
use App\Models\GameItem;
use App\Models\GameSetting;
use App\Models\GameDelay;
use App\Models\GameLog;
use App\Models\Watchdog;

class PlayerController extends Controller
{

    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            $character = GameCharacter::find($user->uuid);
            $skills = GameSkill::forClass($character->class_id);
            return view('game.player', [ 'character' => $character, 'skills' => $skills, ]);
        } else {
            return redirect()->route('game');
        }
    }

    function character_edit($uuid)
    {
        $character = GameCharacter::find($uuid);
        $classes = GameClass::all();
        return view('game.profession_setup', [ 'action' => route('game.play_profession', [ 'uuid' => $character->uuid ]), 'character' => $character, 'classes' => $classes]);
    }

    function character_class(Request $request, $uuid)
    {
        $pro = GameClass::find($request->input('class_id'));
        $character = GameCharacter::find($uuid);
        $character->class_id = $pro->id;
        if ($character->level == 1) {
            $character->max_hp = $pro->base_hp;
            $character->hp = $pro->base_hp;
            $character->max_mp = $pro->base_mp;
            $character->mp = $pro->base_mp;
            $character->ap = $pro->base_ap;
            $character->dp = $pro->base_dp;
            $character->sp = $pro->base_sp;    
        }
        $character->save();
        return view('game.image_setup', [ 'action' => route('game.play_image', [ 'uuid' => $character->uuid ]), 'character' => $character ]);
    }

    function image_edit(Request $request, $uuid)
    {
        $character = GameCharacter::find($uuid);
        return view('game.image_setup', [ 'action' => route('game.play_image', [ 'uuid' => $character->uuid ]), 'character' => $character ]);
    }

    function character_image(Request $request, $uuid)
    {
        $character = GameCharacter::find($uuid);
        $character->image_id = $request->input('image_id');
        $character->save();
        return redirect()->route('game.player');
    }

    public function party()
    {
        $user = User::find(Auth::user()->id);
        $character = GameCharacter::find($user->uuid);
        $party = $character->party;
        return view('game.fundation', [ 'character' => $character, 'party' => $party ]);
    }

    public function get_items(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $items = $char->items;
        return response()->json([ 'items' => $items ]);
    }

    public function skill_cast(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $skill = GameSkill::find($request->input('skill'));
        if ($request->has('item')) {
            $item_id = $request->input('item');
        } else {
            $item_id = null;
        }
        if ($skill->object == 'self') {
            $me->use_skill($skill->id);
        } elseif ($skill->object == 'partner') {
            $me->use_skill($skill->id, $request->input('target'), null, $item_id);
        } elseif ($skill->object == 'party') {
            $me->use_skill($skill->id, null, $me->party_id, $item_id);
        } else {
            $target = GameCharacter::find($request->input('target'));
            if ($skill->object == 'all') {
                $me->use_skill($skill->id, null, $target->party_id, $item_id);
            } else {
                $me->use_skill($skill->id, $target->uuid, null, $item_id);
            }
        }
    }

    public function item_use(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $item = GameItem::find($request->input('item'));
        if ($item->object == 'self') {
            $me->use_item($item->id);
        } elseif ($item->object == 'partner') {
            $me->use_item($item->id, $request->input('target'));
        } elseif ($item->object == 'party') {
            $me->use_item($item->id, null, $me->party_id);
        } else {
            $target = GameCharacter::find($request->input('target'));
            if ($item->object == 'all') {
                $me->use_skill($item->id, null, $target->party_id);
            } else {
                $me->use_skill($item->id, $target->uuid);
            }
        }
    }

}
