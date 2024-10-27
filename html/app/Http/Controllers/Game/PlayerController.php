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
use App\Events\BattleAction;
use App\Events\GameRoomChannel;
use App\Events\GamePartyChannel;
use App\Events\GameCharacterChannel;
use App\Events\GameDialogChannel;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Seats;
use App\Models\GameSence;
use App\Models\GameConfigure;
use App\Models\GameBase;
use App\Models\GameParty;
use App\Models\GameCharacter;
use App\Models\GameClass;
use App\Models\GameSkill;
use App\Models\GameItem;
use App\Models\GameFurniture;
use App\Models\GameEvaluate;
use App\Models\GameQuestion;
use App\Models\GameOption;
use App\Models\GameAnswer;
use App\Models\GameJourney;
use App\Models\GameMonster;
use App\Models\GameMonsterSpawn;
use App\Models\GameDungeon;
use App\Models\Watchdog;
use Carbon\Carbon;

class PlayerController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            $stu = Student::find($user->uuid);
            $character = GameCharacter::find($user->uuid);
            ExitArena::dispatch($character);
            if (!$character) {
                $character = GameCharacter::create([
                    'uuid' => $stu->uuid,
                    'classroom_id' => $stu->class_id,
                    'seat' => $stu->seat,
                    'name' => $stu->realname,
                ]);
            }
            $configure = GameConfigure::findByClass($stu->class_id);
            return view('game.player', [ 'configure' => $configure, 'character' => $character ]);
        } else {
            return redirect()->route('game');
        }
    }

    function character_edit()
    {
        $user = Auth::user();
        $stu = Student::find($user->uuid);
        $character = GameCharacter::find($user->uuid);
        if (!$character) {
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
        $user = Auth::user();
        $stu = Student::find($user->uuid);
        $character = GameCharacter::find($user->uuid);
        if (!$character) {
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
        if (!$kind) {
            $skills = $char->skills();
        } elseif ($kind == 'self') {
            $skills = $char->skills_by_object('self');
        } elseif ($kind == 'enemy') {
            $skills = $char->skills()->filter(function ($sk) {
                return $sk->object == 'target' || $sk->object == 'all';
            });
        } elseif ($kind == 'friend') {
            $skills = $char->skills()->filter(function ($sk) {
                return $sk->object == 'partner' || $sk->object == 'party';
            });
        } elseif ($kind == 'monster') {
            $skills = $char->skills()->reject(function ($sk) {
                return $sk->object == 'partner' || $sk->object == 'party';
            });
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
        if (!$kind) {
            $items = $char->items;
        } elseif ($kind == 'self') {
            $items = $char->items_by_object('self');
        } elseif ($kind == 'enemy') {
            $items = $char->items->filter( function ($item) {
                return $item->object == 'target' || $item->object == 'all';
            });
        } elseif ($kind == 'friend') {
            $items = $char->items->filter( function ($item) {
                return $item->object == 'partner' || $item->object == 'party';
            });
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
            $configure = GameConfigure::findByClass($party->classroom_id);
            return view('game.fundation', [ 'configure' => $configure, 'character' => $character, 'party' => $party, 'bases' => $bases ]);
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
            $item = GameItem::find($request->input('item'));
        } else {
            $item = null;
        }
        if ($skill->object == 'self') {
            $result = $me->use_skill($skill->id);
            $message = $me->name.'對自己施展'.$skill->name;
        } elseif ($skill->object == 'partner') {
            $target = GameCharacter::find($request->input('target'));
            if ($target) {
                if ($item) {
                    $result = $me->use_skill($skill->id, $target->uuid, null, $item->id);
                } else {
                    $result = $me->use_skill($skill->id, $target->uuid);
                }
                $message = $me->name.'對'.$target->name.'施展'.$skill->name;
                if ($item) $message .= $item->name;    
            }
        } elseif ($skill->object == 'party') {
            if ($item) {
                $result = $me->use_skill($skill->id, null, $me->party_id, $item->id);
            } else {
                $result = $me->use_skill($skill->id, null, $me->party_id);
            }
            $message = $me->name.'對全隊施展'.$skill->name;
            if ($item) $message .= $item->name;
        }
        $me->refresh();
        if (isset($message)) broadcast(new GameCharacterChannel($me->stdno, $message));
        return response()->json([ 'skill' => $skill, 'item' => $item, 'result' => ($result ?: ''), 'character' => $me ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function arena_skill(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $skill = GameSkill::find($request->input('skill'));
        if ($request->has('item')) {
            $item = GameItem::find($request->input('item'));
        } else {
            $item = null;
        }
        if ($skill->object == 'self') {
            $me->use_skill($skill->id);
            $message = $me->name.'對自己施展'.$skill->name;
        } elseif ($skill->object == 'partner') {
            $target = GameCharacter::find($request->input('target'));
            if ($target) {
                if ($item) {
                    $me->use_skill($skill->id, $target->uuid, null, $item->id);
                } else {
                    $me->use_skill($skill->id, $target->uuid);
                }
                $message = $me->name.'對'.$target->name.'施展'.$skill->name;
                if ($item) $message .= $item->name;    
            }
        } elseif ($skill->object == 'party') {
            if ($item) {
                $me->use_skill($skill->id, null, $me->party_id, $item->id);
            } else {
                $me->use_skill($skill->id, null, $me->party_id);
            }
            $message = $me->name.'對全隊施展'.$skill->name;
            if ($item) $message .= $item->name;
        } else {
            $target = GameCharacter::find($request->input('target'));
            if ($target) {
                if ($skill->object == 'all') {
                    if ($item) {
                        $me->use_skill($skill->id, null, $target->party_id, $item->id);
                    } else {
                        $me->use_skill($skill->id, null, $target->party_id);
                    }
                    $message = $me->name.'對所有對手施展'.$skill->name;
                    if ($item) $message .= $item->name;
                } else {
                    if ($item) {
                        $me->use_skill($skill->id, $target->uuid, null, $item->id);
                    } else {
                        $me->use_skill($skill->id, $target->uuid);
                    }
                    $message = $me->name.'對'.$target->name.'施展'.$skill->name;
                    if ($item) $message .= $item->name;
                }
            }
        }
        $characters = $me->members();
        foreach ($characters as $char) {
            $char->refresh();
        }
        $namespace = 'arena:'.$me->classroom_id.':battle:'.$me->party->id;
        if (Redis::exists($namespace)) {
            $enemy = Redis::get($namespace);
            if (isset($message)) BattleAction::dispatch($me->party, $message);
            $enemys = GameParty::find($enemy)->members;
            foreach ($enemys as $char) {
                $char->refresh();
            }
            return response()->json([ 'characters' => $characters, 'enemys' => $enemys ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([ 'characters' => $characters ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function item_use(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $item = GameItem::find($request->input('item'));
        if ($item->object == 'self') {
            $result = $me->use_item($item->id);
            $message = $me->name.'對自己使用'.$item->name;
        } elseif ($item->object == 'partner') {
            $target = GameCharacter::find($request->input('target'));
            $result = $me->use_item($item->id, $target->uuid);
            $message = $me->name.'對'.$target->name.'使用'.$item->name;
        } elseif ($item->object == 'party') {
            $result = $me->use_item($item->id, null, $me->party_id);
            $message = $me->name.'對全隊使用'.$item->name;
        } else {
            $target = GameCharacter::find($request->input('target'));
            if ($item->object == 'all') {
                $result = $me->use_skill($item->id, null, $target->party_id);
                $message = $me->name.'對所有對手使用'.$item->name;
            } else {
                $result = $me->use_skill($item->id, $target->uuid);
                $message = $me->name.'對'.$target->name.'使用'.$item->name;
            }
        }
        $me->refresh();
        if (isset($message)) broadcast(new GameCharacterChannel($me->stdno, $message));
        return response()->json([ 'item' => $item, 'result' => ($result ?: ''), 'character' => $me ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function arena_item(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $item = GameItem::find($request->input('item'));
        if ($item->object == 'self') {
            $me->use_item($item->id);
            $message = $me->name.'對自己使用'.$item->name;
        } elseif ($item->object == 'partner') {
            $target = GameCharacter::find($request->input('target'));
            $me->use_item($item->id, $target->uuid);
            $message = $me->name.'對'.$target->name.'使用'.$item->name;
        } elseif ($item->object == 'party') {
            $me->use_item($item->id, null, $me->party_id);
            $message = $me->name.'對全隊使用'.$item->name;
        } else {
            $target = GameCharacter::find($request->input('target'));
            if ($item->object == 'all') {
                $me->use_skill($item->id, null, $target->party_id);
                $message = $me->name.'對所有對手使用'.$item->name;
            } else {
                $me->use_skill($item->id, $target->uuid);
                $message = $me->name.'對'.$target->name.'使用'.$item->name;
            }
        }
        $characters = $me->members();
        foreach ($characters as $char) {
            $char->refresh();
        }
        $namespace = 'arena:'.$me->classroom_id.':battle:'.$me->party->id;
        if (Redis::exists($namespace)) {
            $enemy = Redis::get($namespace);
            if (isset($message)) BattleAction::dispatch($me->party, $message);
            $enemys = GameParty::find($enemy)->members;
            foreach ($enemys as $char) {
                $char->refresh();
            }
            return response()->json([ 'characters' => $characters, 'enemys' => $enemys ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([ 'characters' => $characters ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function furniture_shop()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        ExitArena::dispatch($character);
        $furnitures = GameFurniture::all();
        $configure = GameConfigure::findByClass($character->classroom_id);
        return view('game.furniture_shop', [ 'configure' => $configure, 'character' => $character, 'furnitures' => $furnitures ]);
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
        $configure = GameConfigure::findByClass($character->classroom_id);
        return view('game.item_shop', [ 'configure' => $configure, 'character' => $character, 'items' => $items ]);
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
        $configure = GameConfigure::findByClass($character->classroom_id);
        return view('game.arena', [ 'configure' => $configure, 'character' => $character ]);
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
            $enemy_id = Redis::get($namespace);
            $enemys = [];
            $namespace = 'arena:'.$room.':party:'.$enemy_id;
            $uuids = Redis::smembers($namespace);
            foreach($uuids as $uuid){
                $enemys[] = GameCharacter::find($uuid);
            }
            $namespace = 'arena:'.$room.':action:'.$party->id;
            $our_actions = Redis::smembers($namespace);
            $we_done = count($our_actions) == $character->members()->count();
            $namespace = 'arena:'.$room.':action:'.$enemy_id;
            $enemy_actions = Redis::smembers($namespace);
            $enemy_party = GameParty::find($enemy_id);
            $enemy_done = count($enemy_actions) == $enemy_party->members->count();
            if ($we_done && $enemy_done) {
                BattleEnd::dispatch($party, $enemy_party);
            }
            return response()->json([ 'characters' => $characters, 'enemy' => $enemy_id, 'enemys' => $enemys, 'our_actions' => $our_actions, 'enemy_actions' => $enemy_actions ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
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
                broadcast(new GameCharacterChannel($character->stdno, '請立刻前往競技場集合！'));
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
                broadcast(new GameDialogChannel($character->uuid, $leader->uuid, 'invite'));
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
            broadcast(new GameDialogChannel($character->uuid, $object->uuid, 'accept_invite'));
            return response()->json([ 'success' => 'ok' ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function reject_battle(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        $object = GameCharacter::find($request->input('from'));
        if ($object) {
            broadcast(new GameDialogChannel($character->uuid, $object->uuid, 'reject_invite'));
        }
    }

    public function dungeon()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        ExitArena::dispatch($character);
        $configure = GameConfigure::findByClass($character->classroom_id);
        return view('game.dungeon', [ 'configure' => $configure, 'character' => $character ]);
    }

    public function get_dungeons()
    {
        $character = GameCharacter::find(Auth::user()->uuid);
        $dungeons = $character->dungeons();
        return response()->json([ 'dungeons' => $dungeons ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function enter_dungeon(Request $request)
    {
        $user = Auth::user();
        $character = GameCharacter::find($user->uuid);
        $dungeon = GameDungeon::find($request->input('dungeon_id'));
        $questions = $dungeon->evaluate->random();
        $monster = GameMonster::find($dungeon->monster_id);
        $spawn = $monster->spawn($user->uuid);
        GameAnswer::where('uuid', $user->uuid)->where('score', 0)->delete();
        $answer = GameAnswer::create([
            'uuid' => $user->uuid,
            'dungeon_id' => $dungeon->id,
            'evaluate_id' => $dungeon->evaluate_id,
            'classroom_id' => $dungeon->classroom_id,
            'seat' => $character->seat,
            'student' => $character->name,
            'score' => 0,
            'tested_at' => Carbon::now(),
        ]);
        return response()->json([ 'dungeon' => $dungeon, 'answer' => $answer, 'questions' => $questions, 'monster' => $spawn ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function exit_dungeon()
    {
        $user = Auth::user();
        GameMonsterSpawn::where('uuid', $user->uuid)->delete();
        return response()->json([ 'success' => 'ok' ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function monster_respawn(Request $request)
    {
        $user = Auth::user();
        $monster = GameMonster::find($request->input('monster_id'));
        $spawn = $monster->spawn($user->uuid);
        return response()->json([ 'monster' => $spawn ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function monster_attack(Request $request)
    {
        $spawn = GameMonsterSpawn::find($request->input('spawn_id'));
        $character = GameCharacter::find($spawn->uuid);
        $response = $spawn->attack();
        $spawn->refresh();
        $character->refresh();
        return response()->json([ 'skill' => $response['skill'], 'result' => $response['result'], 'character' => $character, 'monster' => $spawn ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function skill_monster(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $skill = GameSkill::find($request->input('skill'));
        if ($request->has('item')) {
            $item = GameItem::find($request->input('item'));
        } else {
            $item = null;
        }
        if ($skill->object == 'self') {
            $result = $me->use_skill($skill->id);
        } else {
            if ($item) {
                $result = $me->use_skill_on_monster($skill->id, $request->input('target'), $item->id);
            } else {
                $result = $me->use_skill_on_monster($skill->id, $request->input('target'));
            }
        }
        $me->refresh();
        $spawn = GameMonsterSpawn::find($request->input('target'));
        if ($item) {
            return response()->json([ 'skill' => $skill, 'item' => $item, 'result' => ($result ?: ''), 'character' => $me, 'monster' => $spawn ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([ 'skill' => $skill, 'result' => ($result ?: ''), 'character' => $me, 'monster' => $spawn ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function item_monster(Request $request)
    {
        $me = GameCharacter::find($request->input('self'));
        $item = GameItem::find($request->input('item'));
        if ($item->object == 'self') {
            $result = $me->use_item($item->id);
        } else {
            $result = $me->use_item_on_monster($item->id, $request->input('target'));
        }
        $me->refresh();
        $spawn = GameCharacter::find($request->input('target'));
        return response()->json([ 'item' => $item, 'result' => ($result ?: ''), 'character' => $me, 'monster' => $spawn ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function journey(Request $request) {
        $dungeon = GameDungeon::find($request->input('dungeon'));
        $answer = GameAnswer::find($request->input('answer'));
        $question = GameQuestion::find($request->input('question'));
        $option = GameOption::find($request->input('option'));
        $correct = ($question->answer == $option->id);
        GameJourney::create([
            'evaluate_id' => $dungeon->evaluate_id,
            'answer_id' => $answer->id,
            'question_id' => $question->id,
            'option_id' => $option->id,
            'is_correct' => $correct,
        ]);
        if ($correct) {
            $answer->score += $question->score;
            $answer->save();
        }
    }

}
