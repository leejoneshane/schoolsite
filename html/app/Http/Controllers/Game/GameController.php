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
            $student = Student::find($user->uuid);
            $character = GameCharacter::find($user->uuid);
            return view('game.character', [ 'student' => $student, 'character' => $character ]);
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
            if (!$char) {
                GameCharacter::create([
                    'uuid' => $stu->uuid,
                    'seat' => $stu->seat,
                    'name' => $stu->realname,
                ]);
            }
        }
        $partyless = GameCharacter::findNoParty($room_id);
        $positive_rules = GameSetting::positive($request->user()->uuid);
        $negative_rules = GameSetting::negative($request->user()->uuid);
        $items = GameItem::all();
        return view('game.roster', [ 'teacher' => $teacher, 'room' => $room, 'parties' => $parties, 'partyless' => $partyless, 'positive_rules' => $positive_rules, 'negative_rules' => $negative_rules, 'items' => $items ]);
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

    public function fast_edit($uuid)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') return response()->json(['error' => '您沒有權限使用此功能！'], 403);
        $character = GameCharacter::find($uuid);
        $room = $character->student->class_id;
        $parties = GameParty::findByClass($room);
        $classes = GameClass::all();
        return view('game.character_edit', [ 'character' => $character, 'parties' => $parties, 'classes' => $classes ]);
    }

    public function fast_update(Request $request, $uuid)
    {
        $character = GameCharacter::find($uuid);
        if ($character->party_id != $request->input('party')) {
            $character->party_id = $request->input('party');
        }
        if (!empty($request->input('title'))) {
            $character->title = $request->input('title');
        }
        if ($character->class_id != $request->input('profession')) {
            $pro = GameClass::find($request->input('profession'));
            $character->class_id = $pro->id;
            $character->level = 1;
            $character->max_hp = $pro->base_hp;
            $character->max_mp = $pro->base_mp;
            $character->ap = $pro->base_ap;
            $character->dp = $pro->base_dp;
            $character->sp = $pro->base_sp;
            $character->save();
            $character->levelup();
        }
        $room = $character->student->class_id;
        return redirect()->route('game.room', [ 'room_id' => $room ]);
    }

    public function positive_act(Request $request)
    {
        $add = [];
        if ($request->input('rule') > 0) {
            $rule = GameSetting::find($request->input('rule'));
            $message = '因為'.$rule->description.'獲得上天的祝福：';
        } else {
            $message = '因為'.$request->input('reason').'獲得上天的祝福：';
        }
        if ($request->input('xp') > 0) {
            $xp = $request->input('xp');
            $add[] = '經驗值' . $xp . '點';
        }
        if ($request->input('gp') > 0) {
            $gp = $request->input('gp');
            $add[] = '金幣' . $gp . '枚';
        }
        if ($request->input('item') > 0) {
            $item = GameItem::find($request->input('item'));
            $add[] = '道具' . $item->name . '一個';
        }
        $message .= implode('、', $add).'。';
        $uuids = explode(',', $request->input('uuids'));
        foreach ($uuids as $uuid) {
            $character = GameCharacter::find($uuid);
            if ($character->class_id) {
                if (isset($xp)) $character->xp += $xp;
                if (isset($gp)) $character->gp += $gp;
                if (isset($item)) $character->get_item($item->id);
                $character->save();
                GameLog::create([
                    'classroom_id' => session('gameclass'),
                    'uuid' => $request->input('uuid'),
                    'character_uuid' => $character->uuid,
                    'content' => $character->seat.' '.$character->name.$message,
                ]);
            }
        }
    }

    public function negative_act(Request $request)
    {
        $add = [];
        if ($request->input('rule') > 0) {
            $rule = GameSetting::find($request->input('rule'));
            $message = '因為'.$rule->description.'受到天罰損失：';
        } else {
            $message = '因為'.$request->input('reason').'受到天罰損失：';
        }
        if ($request->input('hp') > 0) {
            $hp = $request->input('hp');
            $add[] = '生命力' . $hp . '點';
        }
        if ($request->input('mp') > 0) {
            $mp = $request->input('mp');
            $add[] = '法力（行動力）' . $mp . '點';
        }
        $message .= implode('、', $add).'。';
        $uuids = explode(',', $request->input('uuids'));
        foreach ($uuids as $uuid) {
            $character = GameCharacter::find($uuid);
            if ($character->class_id) {
                if ($character->status != DEAD) {
                    if (isset($hp)) $character->hp -= $hp;
                }
                if ($character->status != COMA) {
                    if (isset($mp)) $character->mp -= $mp;
                }
                $character->save();
                GameLog::create([
                    'classroom_id' => session('gameclass'),
                    'uuid' => $request->input('uuid'),
                    'character_uuid' => $character->uuid,
                    'content' => $character->seat.' '.$character->name.$message,
                ]);
            }
        }
    }

    public function negative_delay(Request $request)
    {
        if ($request->input('rule') > 0) {
            $rule = GameSetting::find($request->input('rule'));
            GameDelay::create([
                'classroom_id' => session('gameclass'),
                'uuid' => $request->input('uuid'),
                'characters' => $request->input('uuids'),
                'rule' => $rule->id,
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
            ]);
        } else {
            GameDelay::create([
                'classroom_id' => session('gameclass'),
                'uuid' => $request->input('uuid'),
                'characters' => $request->input('uuids'),
                'reason' => $request->input('reason'),
                'hp' => $request->input('hp'),
                'mp' => $request->input('mp'),
            ]);
        }
    }

    function regress($delay_id) {
        $delay = GameDelay::find($delay_id);
        $add = [];
        if ($delay->rule) {
            $rule = GameSetting::find($delay->rule);
            $message = '因為'.$rule->description.'受到天罰損失：';
        } else {
            $message = '因為'.$rule->reason.'受到天罰損失：';
        }
        if ($delay->hp > 0) {
            $add[] = '生命力' . $delay->hp . '點';
        }
        if ($delay->mp > 0) {
            $add[] = '法力（行動力）' . $delay->mp . '點';
        }
        $message .= implode('、', $add).'。';
        $uuids = explode(',', $delay->characters);
        foreach ($uuids as $uuid) {
            $character = GameCharacter::find($uuid);
            if ($character->class_id) {
                if ($character->status != DEAD) {
                    if (isset($hp)) $character->hp -= $delay->hp;
                }
                if ($character->status != COMA) {
                    if (isset($mp)) $character->mp -= $delay->mp;
                }
                $character->save();
                GameLog::create([
                    'classroom_id' => $delay->classroom_id,
                    'uuid' => $delay->uuid,
                    'character_uuid' => $character->uuid,
                    'content' => $character->seat.' '.$character->name.$message,
                ]);
            }
        }
        $delay->act = true;
        $delay->save();
    }

    function pickup(Request $request, $room_id)
    {
        $room = Classroom::find($room_id);
        $parties = GameParty::wheel($room_id);
        $characters = GameCharacter::wheel($room_id);
        $positive_rules = GameSetting::positive($request->user()->uuid);
        $negative_rules = GameSetting::negative($request->user()->uuid);
        $items = GameItem::all();
        return view('game.wheel', [ 'room' => $room, 'parties' => $parties, 'characters' => $characters, 'positive_rules' => $positive_rules, 'negative_rules' => $negative_rules, 'items' => $items ]);
    }

}
