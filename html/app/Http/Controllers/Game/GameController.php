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
use App\Models\Watchdog;

class GameController extends Controller
{

    public function index(Request $request)
    {
        $request->session()->forget('gameclass');
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

    public function classroom(Request $request, $room_id)
    {
        $request->session()->put('gameclass', $room_id);
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
        return view('game.roster', [ 'teacher' => $teacher, 'room' => $room, 'parties' => $parties, 'partyless' => $partyless ]);
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

}
