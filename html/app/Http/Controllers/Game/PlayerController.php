<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Events\EnterArena;
use App\Events\ExitArena;
use App\Events\BattleStart;
use App\Events\BattleEnd;
use App\Events\GameRoomChannel;
use App\Events\GamePartyChannel;
use App\Events\GameCharacterChannel;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Seats;
use App\Models\GameSence;
use App\Models\GameBase;
use App\Models\GameParty;
use App\Models\GameCharacter;
use App\Models\GameClass;
use App\Models\GameSkill;
use App\Models\GameItem;
use App\Models\GameFurniture;
use App\Models\GameSetting;
use App\Models\GameDelay;
use App\Models\GameLog;
use App\Models\Watchdog;

class PlayerController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            $character = GameCharacter::find(Auth::user()->uuid);
            ExitArena::dispatch($character);
            if (!$character) {
                $stu = profile();
                GameCharacter::create([
                    'uuid' => $stu->uuid,
                    'classroom_id' => $stu->class_id,
                    'seat' => $stu->seat,
                    'name' => $stu->realname,
                ]);
            }    
            $skills = GameSkill::forClass($character->class_id);
            return view('game.player', [ 'character' => $character, 'skills' => $skills, ]);
        } else {
            return redirect()->route('game');
        }
    }

    function character_edit()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        if (!$character) {
            $stu = profile();
            GameCharacter::create([
                'uuid' => $stu->uuid,
                'classroom_id' => $stu->class_id,
                'seat' => $stu->seat,
                'name' => $stu->realname,
            ]);
        }
        $classes = GameClass::all();
        return view('game.profession_setup', [ 'action' => route('game.player_profession'), 'character' => $character, 'classes' => $classes]);
    }

    function character_class(Request $request)
    {
        $pro = GameClass::find($request->input('class_id'));
        $character = GameCharacter::find(Auth::user()->uuid);
        $character->change_class($pro->id);
        return view('game.image_setup', [ 'action' => route('game.player_image'), 'character' => $character ]);
    }

    function image_edit(Request $request)
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        if (!$character) {
            $stu = profile();
            GameCharacter::create([
                'uuid' => $stu->uuid,
                'classroom_id' => $stu->class_id,
                'seat' => $stu->seat,
                'name' => $stu->realname,
            ]);
        }
        return view('game.image_setup', [ 'action' => route('game.player_image'), 'character' => $character ]);
    }

    function character_image(Request $request)
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        $character->image_id = $request->input('image_id');
        $character->save();
        return redirect()->route('game.player');
    }

    public function get_skills(Request $request)
    {
        $uuid = $request->input('uuid');
        $kind = $request->input('kind');
        $char = GameCharacter::find($uuid);
        if (!$char->class_id) {
            return response()->json([]);
        }
        if ($kind == 'self') {
            $skills = $char->skills_by_object('self');
        } elseif ($kind == 'enemy') {
            $skills = $char->skills_by_object('target')->merge($char->skills_by_object('all'));
        } else {
            $skills = $char->skills_by_object('partner')->merge($char->skills_by_object('party'));
        }
        return response()->json([ 'skills' => $skills, 'level' => $char->level ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get_items(Request $request)
    {
        $uuid = $request->input('uuid');
        $kind = $request->input('kind');
        $char = GameCharacter::find($uuid);
        if (!$char->class_id) {
            return response()->json([]);
        }
        if ($kind == 'self') {
            $items = $char->items_by_object('self');
        } elseif ($kind == 'enemy') {
            $items = $char->items_by_object('target')->merge($char->items_by_object('all'));
        } else {
            $items = $char->items_by_object('partner')->merge($char->items_by_object('party'));
        }
        return response()->json([ 'items' => $items, 'money' => $char->gp ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get_furnitures(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $furnitures = $char->party->furnitures;
        $treasury = $char->party->treasury;
        return response()->json([ 'furnitures' => $furnitures, 'treasury' => $treasury ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function party()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        ExitArena::dispatch($character);
        $party = $character->party;
        if ($party) {
            if (!($party->uuid)) {
                $party->uuid = $character->uuid;
                $party->save();
            }
            $bases = GameBase::all();
            return view('game.fundation', [ 'character' => $character, 'party' => $party, 'bases' => $bases ]);
        } else {
            return redirect()->route('game.player')->with('error', '您尚未加入公會，無法使用據點！');
        }
    }

    public function party_name(Request $request)
    {
        $party = GameParty::find($request->input('party'));
        $party->name = $request->input('name');
        $party->save();
        return response()->json([ 'success' => $party ]);
    }

    public function party_desc(Request $request)
    {
        $party = GameParty::find($request->input('party'));
        $party->description = $request->input('desc');
        $party->save();
        return response()->json([ 'success' => $party ]);
    }

    public function party_leader(Request $request)
    {
        $party = GameParty::find($request->input('party'));
        $party->uuid = $request->input('leader');
        $party->save();
        return response()->json([ 'success' => $party ]);
    }

    public function party_base(Request $request)
    {
        $party = GameParty::find($request->input('party'));
        $party->change_foundation($request->input('base'));
        return response()->json([ 'success' => $party ]);
    }

    public function donate(Request $request)
    {
        $banker = GameCharacter::find($request->input('uuid'));
        $party = GameParty::find($request->input('party'));
        $cash = $request->input('cash');
        $banker->gp -= $cash;
        $banker->save();
        $party->treasury += $cash;
        $party->save();
        return response()->json([ 'gp' => $banker->gp, 'treasury' => $party->treasury ]);
    }

    public function given(Request $request)
    {
        $owner = GameCharacter::find($request->input('uuid'));
        $target = GameCharacter::find($request->input('target'));
        $item_id = $request->input('item');
        $owner->loss_item($item_id);
        $target->get_item($item_id);
        return response()->json([ 'success' => $item_id ]);
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

    public function furniture_shop()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        ExitArena::dispatch($character);
        $furnitures = GameFurniture::all();
        return view('game.furniture_shop', [ 'character' => $character, 'furnitures' => $furnitures ]);
    }

    public function buy_furniture(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $fur_id = $request->input('furniture');
        $char->party->buy_furniture($fur_id);
        return response()->json([ 'treasury' => $char->party->treasury ]);
    }

    public function sell_furniture(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $fur_id = $request->input('furniture');
        $cash = $request->input('cash');
        if (!$cash) $cash = 0;
        $char->party->buy_furniture($fur_id, $cash);
        return response()->json([ 'treasury' => $char->party->treasury ]);
    }

    public function item_shop()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        ExitArena::dispatch($character);
        $items = GameItem::all();
        return view('game.item_shop', [ 'character' => $character, 'items' => $items ]);
    }

    public function buy_item(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $item_id = $request->input('item');
        $char->buy_item($item_id);
        return response()->json([ 'gp' => $char->gp ]);
    }

    public function sell_item(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $item_id = $request->input('item');
        $char->sell_item($item_id);
        return response()->json([ 'gp' => $char->gp ]);
    }

    public function arena()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        EnterArena::dispatch($character);
        return view('game.arena', [ 'character' => $character ]);
    }

    public function refresh_arena(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        $party = $character->party;
        $room = $party->classroom_id;
        $namespace = 'arena:'.$room.':party:'.$party->id;
        $uuids = Redis::smembers($namespace);
        foreach($uuids as $uuid){
            $characters[] = GameCharacter::find($uuid);
        }

        $namespace = 'arena:'.$room.':battle:'.$party->id;
        if (Redis::exists($namespace)) {
            $enemy_party = Redis::get($namespace);
            $enemys = [];
            $enemy_partyobj = GameParty::find($enemy_party);
            if ($enemy_partyobj) {
                $namespace = 'arena:'.$room.':party:'.$enemy_party;
                $uuids = Redis::smembers($namespace);
                foreach($uuids as $uuid){
                    $enemys[] = GameCharacter::find($uuid);
                }
            }
            return response()->json([ 'characters' => $characters, 'enemy' => $enemy_party, 'enemys' => $enemys ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } else {
            $parties = null;
            $namespace = 'arena:'.$room.':ready';
            $pids = Redis::smembers($namespace);
            foreach($pids as $pid){
                $namespace = 'arena:'.$room.':battle:'.$pid;
                if (!Redis::exists($namespace) && $pid != $party->id) {
                    $parties[] = GameParty::find($pid);
                }
            }
            return response()->json([ 'characters' => $characters, 'parties' => $parties ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function come_arena(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        $room = $character->party->classroom_id;
        $namespace = 'arena:'.$room.':party:'.$character->party_id;
        $uuids = Redis::smembers($namespace);
        $not_in = $character->teammate()->reject( function ($m) use ($uuids) {
            return in_array($m->uuid, $uuids);
        });
        if ($not_in->count() > 0) {
            foreach ($not_in as $m) {
                broadcast(new GameCharacterChannel($character->uuid, $m->uuid, '請立刻前往競技場集合！'));
            }
        }
    }

    public function invite_battle(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        $room = $character->party->classroom_id;
        $party = GameParty::find($request->input('party'));
        if ($party) {
            $namespace = 'arena:'.$room.':battle:'.$party->id;
            if (!Redis::exists($namespace)) {
                $leader = $party->leader;
                broadcast(new GameCharacterChannel($character->uuid, $leader->uuid, null, 'invite'));
            }    
        }
    }

    public function accept_battle(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        $room = $character->party->classroom_id;
        $object = GameCharacter::find($request->input('from'));
        $namespace = 'arena:'.$room.':battle:'.$object->party->id;
        if (Redis::exists($namespace)) {
            return response()->json([ 'error' => '對方已經在戰鬥中，對戰邀請已失效！' ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } else {
            BattleStart::dispatch($character->party, $object->party);
            broadcast(new GameCharacterChannel($character->uuid, $object->uuid, null, 'accept_invite'));
            return response()->json([ 'success' => 'ok' ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function reject_battle(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        $object = GameCharacter::find($request->input('from'));
        if ($object) {
            broadcast(new GameCharacterChannel($character->uuid, $object->uuid, null, 'reject_invite'));
        }
    }

}
