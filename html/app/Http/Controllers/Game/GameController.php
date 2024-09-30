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

class GameController extends Controller
{

    public function index(Request $request)
    {
        $request->session()->forget('gameclass');
        $request->session()->forget('viewclass');
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $classes = $teacher->classrooms;
            foreach ($classes as $cls) {
                if (GameSence::is_lock($cls->id)) {
                    $cls->lockBy = GameSence::lockBy($cls->id)->uuid;
                } else {
                    $cls->lockBy = null;
                }
                $error = [];
                $parties = GameParty::findByClass($cls->id);
                if ($parties->count() < 1) {
                    $error[] = '尚未分組';
                }
                $count = 0;
                $data = [];
                $noparty = GameCharacter::findNoParty($cls->id);
                if ($noparty->count() > 0) {
                    foreach ($noparty as $c) {
                        $count ++;
                        $data[] = $c->name;
                    }
                    if ($count < 10) {
                        $error[] = '以下學生沒有組別：'.implode('、', $data);
                    } else {
                        $error[] = '共有'.$count.'位學生沒有組別';
                    }
                }
                $count1 = 0;
                $count2 = 0;
                $data1 = [];
                $data2 = [];
                $characters = GameCharacter::findByClass($cls->id);
                foreach ($characters as $char) {
                    if (!$char->class_id) {
                        $count1 ++;
                        $data1[] = $char->name;
                    }
                    if (!$char->image_id) {
                        $count2 ++;
                        $data2[] = $char->name;
                    }
                }
                if ($count1 > 0) {
                    if ($count1 < 10) {
                        $error[] = '以下學生尚未設定職業：'.implode('、', $data1);
                    } else {
                        $error[] = '共有'.$count1.'位尚未設定職業';
                    }
                }
                if ($count2 > 0) {
                    if ($count2 < 10) {
                        $error[] = '以下學生尚未選擇角色圖像：'.implode('、', $data2);
                    } else {
                        $error[] = '共有'.$count2.'位尚未選擇角色圖像';
                    }
                }
                if (count($error) > 0) {
                    $cls->error = implode('，', $error).'。';
                } else {
                    $cls->error = null;
                }
            }
            return view('game.index', [ 'classes' => $classes ]);
        } else {
            return redirect()->route('game.player');
        }
    }

    public function lock(Request $request)
    {
        $room_id = $request->input('room_id');
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') return response()->json(['error' => '您沒有權限使用此功能！'], 403);
        $uuid = $user->uuid;
        if ($request->input('lockdown') == 'yes') {
            $result = GameSence::lock($room_id, $uuid);
        } else {
            $result = GameSence::unlock($room_id, $uuid);
        }
        if ($result == LOCKED) {
            $lock = GameSence::find($room_id);
            Watchdog::watch($request, '遊戲鎖定：' . $lock->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        if ($result == UNLOCKED) {
            Watchdog::watch($request, '遊戲解鎖：' . $room_id);
        }
        return response()->json(['success' => $result]);
    }

    public function health()
    {
        return response()->json([ 'health' => GameSence::lockByMe(session('gameclass')) ]);
    }

    public function classroom(Request $request, $room_id)
    {
        if (locked($room_id)) {
            $request->session()->put('gameclass', $room_id);
            $request->session()->forget('viewclass');
        } else {
            $request->session()->forget('gameclass');
            $request->session()->put('viewclass', $room_id);
        }
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        $teacher = Teacher::find(Auth::user()->uuid);
        $room = Classroom::find($room_id);
        $parties = GameParty::findByClass($room_id);
        if ($parties->count() < 1) {
            $seats = Seats::findByClass($room_id)->first();
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
                        GameCharacter::create([
                            'uuid' => $stu->uuid,
                            'classroom_id' => $room_id,
                            'party_id' => $party->id,
                            'seat' => $stu->seat,
                            'name' => $stu->realname,
                        ]);
                    }
                }
                $parties = GameParty::findByClass($room_id);
            }
        }
        foreach ($room->students as $stu) {
            $char = GameCharacter::find($stu->uuid);
            if ($char) {
                if ($char->classroom_id != $room_id) {
                    $char->classroom_id = $room_id;
                    $char->seat = $stu->seat;
                    $char->name = $stu->realname;
                    $char->save();
                }
            } else {
                GameCharacter::create([
                    'uuid' => $stu->uuid,
                    'classroom_id' => $room_id,
                    'seat' => $stu->seat,
                    'name' => $stu->realname,
                ]);
            }
        }
        $partyless = GameCharacter::findNoParty($room_id);
        $positive_rules = GameSetting::positive($request->user()->uuid);
        $negative_rules = GameSetting::negative($request->user()->uuid);
        $items = GameItem::all();
        $classes = GameClass::all();
        return view('game.roster', [ 'teacher' => $teacher, 'room' => $room, 'parties' => $parties, 'partyless' => $partyless, 'positive_rules' => $positive_rules, 'negative_rules' => $negative_rules, 'items' => $items, 'classes' => $classes ]);
    }

    public function absent(Request $request)
    {
        $uuid = $request->input('uuid');
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') return response()->json(['error' => '您沒有權限使用此功能！'], 403);
        $char = GameCharacter::find($uuid);
        if ($request->input('absent') == 'yes') {
            $char->absent = true;
            $char->save();
        } else {
            $char->absent = false;
            $char->save();
        }
        return response()->json(['success' => $char->absent]);
    }

    public function fast_update(Request $request)
    {
        $character = GameCharacter::find($request->input('uuid'));
        if ($character->party_id != $request->input('party')) {
            $character->party_id = $request->input('party');
        }
        if (!empty($request->input('title'))) {
            $character->title = $request->input('title');
        }
        if ($character->class_id != $request->input('profession')) {
            $pro = GameClass::find($request->input('profession'));
            $character->change_class($pro->id);
        }
    }

    public function get_skills(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        if (!$char->class_id) {
            return response()->json([]);
        }
        $skills = $char->passive_skills();
        return response()->json([ 'skills' => $skills ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get_items(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        $items = $char->useable_items();
        return response()->json([ 'items' => $items ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get_teammate(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        return response()->json([ 'teammate' => $char->teammate ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function get_character(Request $request)
    {
        $uuid = $request->input('uuid');
        $char = GameCharacter::find($uuid);
        return response()->json($char)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
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

    public function positive_act(Request $request)
    {
        $teacher = $request->user()->uuid;
        $characters = explode(',', $request->input('uuids'));
        if ($request->input('rule') > 0) {
            $rule_id = $request->input('rule');
        } else {
            $rule_id = null;
        }
        if ($request->input('reason')) {
            $reason = $request->input('reason');
        } else {
            $reason = null;
        }
        if ($request->input('xp') > 0) {
            $xp = $request->input('xp');
        } else {
            $xp = null;
        }
        if ($request->input('gp') > 0) {
            $gp = $request->input('gp');
        } else {
            $gp = null;
        }
        if ($request->input('item') > 0) {
            $item_id = $request->input('item');
        } else {
            $item_id = null;
        }
        GameSetting::positive_act($teacher, $characters, $rule_id, $reason, $xp, $gp, $item_id);
    }

    public function negative_act(Request $request)
    {
        $teacher = $request->user()->uuid;
        $characters = explode(',', $request->input('uuids'));
        if ($request->input('rule') > 0) {
            $rule_id = $request->input('rule');
        } else {
            $rule_id = null;
        }
        if ($request->input('reason')) {
            $reason = $request->input('reason');
        } else {
            $reason = null;
        }
        if ($request->input('hp') > 0) {
            $hp = $request->input('hp');
        } else {
            $hp = null;
        }
        if ($request->input('mp') > 0) {
            $mp = $request->input('mp');
        } else {
            $mp = null;
        }
        GameSetting::negative_act($teacher, $characters, $rule_id, $reason, $hp, $mp);
    }

    public function negative_delay(Request $request)
    {
        $teacher = $request->user()->uuid;
        $characters = explode(',', $request->input('uuids'));
        if ($request->input('rule') > 0) {
            $rule = GameSetting::find($request->input('rule'));
            GameDelay::create([
                'classroom_id' => session('gameclass'),
                'uuid' => $teacher,
                'characters' => $characters,
                'rule' => $rule->id,
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
            ]);
        } else {
            GameDelay::create([
                'classroom_id' => session('gameclass'),
                'uuid' => $teacher,
                'characters' => $characters,
                'reason' => $request->input('reason'),
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
            ]);
        }
    }

    public function regress($delay_id) {
        $delay = GameDelay::find($delay_id);
        GameSetting::negative_act($delay->uuid, $delay->characters, $delay->rule, $delay->reason, $delay->hp, $delay->mp);
        $delay->act = true;
        $delay->save();
        return redirect()->back();
    }

    public function pickup($room_id)
    {
        $room = Classroom::find($room_id);
        $positive_rules = GameSetting::positive(Auth::user()->uuid);
        $negative_rules = GameSetting::negative(Auth::user()->uuid);
        $items = GameItem::all();
        return view('game.wheel', [ 'room' => $room, 'positive_rules' => $positive_rules, 'negative_rules' => $negative_rules, 'items' => $items ]);
    }

    public function random_pickup(Request $request, $room_id)
    {
        if ($request->input('type') == 0) {
            $uuids = [];
            $picks = GameCharacter::wheel($room_id);
            if ($picks->count() > 0) {
                $pick = $picks->random();
                $pick->pick_up ++;
                $pick->save();
                $uuids[] = $pick;
            }
            return response()->json([ 'type' => 0, 'uuids' => $uuids ]);    
        } else {
            $uuids = [];
            $picks = GameParty::wheel($room_id);
            if ($picks->count() > 0) {
                $pick = $picks->random();
                $pick->pick_up ++;
                $pick->save();
                foreach ($pick->members as $m) {
                    if ($m->absent == 0) {
                        $m->pick_up ++;
                        $m->save();
                        $uuids[] = $m;
                    }
                }
                return response()->json([ 'type' => 1, 'name' => $pick->name, 'uuids' => $uuids ]);
            }
            return response()->json([ 'type' => 1, 'name' => '', 'uuids' => [] ]);
        }
    }

    public function timer($room_id)
    {
        $room = Classroom::find($room_id);
        $items = GameItem::all();
        $parties = GameParty::findByClass($room_id);
        return view('game.timer', [ 'room' => $room, 'parties' => $parties, 'items' => $items ]);
    }

    public function silence($room_id)
    {
        $room = Classroom::find($room_id);
        $items = GameItem::all();
        $characters = GameCharacter::withoutAbsent($room_id);
        return view('game.silence', [ 'room' => $room, 'characters' => $characters, 'items' => $items ]);
    }

}
