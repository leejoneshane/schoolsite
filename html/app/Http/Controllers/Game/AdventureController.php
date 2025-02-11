<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\GameCharacter;
use App\Models\GameItem;
use App\Models\GameMap;
use App\Models\GameAdventure;
use App\Models\GameProcess;
use App\Models\GameWorksheet;
use App\Models\GameTask;
use App\Models\Watchdog;
use App\Events\GameCharacterChannel;
use Carbon\Carbon;

class AdventureController extends Controller
{

    public function worksheets()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $worksheets = GameWorksheet::findByUuid($user->uuid);
            return view('game.worksheets', [ 'worksheets' => $worksheets ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_add()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $grades = Grade::all();
            $maps = GameMap::all();
            return view('game.worksheet_add', [ 'teacher' => $teacher, 'grades' => $grades, 'maps' => $maps ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_insert(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameWorksheet::create([
                'uuid' => $user->uuid,
                'title' => $request->input('title'),
                'subject' => $request->input('subject'),
                'description' => $request->input('desc'),
                'grade_id' => $request->input('grade'),
                'map_id' => $request->input('map'),
                'intro' => nl2br($request->input('intro')),
                'share' => $request->input('share') == 'yes',
            ]);
            Watchdog::watch($request, '新增學習單：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.worksheets')->with('success', '學習單'.$e->title.'新增成功!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_edit($worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $grades = Grade::all();
            $maps = GameMap::all();
            $worksheet = GameWorksheet::find($worksheet_id);
            return view('game.worksheet_edit', [ 'teacher' => $teacher, 'worksheet' => $worksheet, 'grades' => $grades, 'maps' => $maps ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_update(Request $request, $worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameWorksheet::find($worksheet_id);
            $e->update([
                'title' => $request->input('title'),
                'subject' => $request->input('subject'),
                'description' => $request->input('desc'),
                'grade_id' => $request->input('grade'),
                'map_id' => $request->input('map'),
                'intro' => nl2br($request->input('intro')),
                'share' => $request->input('share') == 'yes',
            ]);
            Watchdog::watch($request, '修改學習單：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.worksheets')->with('success', '學習單'.$e->title.'已經修改!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_remove(Request $request, $worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameWorksheet::find($worksheet_id);
            Watchdog::watch($request, '刪除學習單：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $e->delete();
            GameAdventure::where('worksheet_id', $worksheet_id)->delete();
            GameProcess::where('worksheet_id', $worksheet_id)->delete();
            return redirect()->route('game.worksheets')->with('success', '評量已經刪除!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_manage($worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $worksheet = GameWorksheet::find($worksheet_id);
            $items = GameItem::all();
            return view('game.worksheet_manage', [ 'worksheet' => $worksheet, 'items' => $items ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_view($worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $worksheet = GameWorksheet::find($worksheet_id);
            $items = GameItem::all();
            return view('game.worksheet_view', [ 'worksheet' => $worksheet, 'items' => $items ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function worksheet_duplicate($worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $old = GameWorksheet::find($worksheet_id);
            $next_id = $old->next_task;
            $new = $old->replicate();
            $new->uuid = $user->uuid;
            $new->share = false;
            $new->save();
            $task = GameTask::find($next_id);
            $newt = $task->replicate();
            $newt->worksheet_id = $new->id;
            $newt->save();
            $new->next_task = $newt->id;
            $new->save();
            $prev = $newt;
            $next_id = $task->next_task;
            while ($next_id != null) {
                $task = GameTask::find($next_id);
                $newt = $task->replicate();
                $newt->worksheet_id = $new->id;
                $newt->save();
                $prev->next_task = $newt->id;
                $prev->save();
                $prev = $newt;
                $next_id = $task->next_task;
            }
            return redirect()->route('game.worksheets')->with('success', '已為您複製學習單！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function task_insert(Request $request)
    {
        $w = GameWorksheet::find($request->input('wid'));
        $e = GameTask::create([
            'title' => $request->input('title'),
            'worksheet_id' => $w->id,
            'coordinate_x' => $request->input('x'),
            'coordinate_y' => $request->input('y'),
            'story' => $request->input('story'),
            'task' => $request->input('task'),
            'review' => $request->input('review') == 'yes',
            'reward_xp' => $request->input('xp'),
            'reward_gp' => $request->input('gp'),
            'reward_item' => $request->input('item'),
        ]);
        if (!$w->next_task) {
            $w->next_task = $e->id;
            $w->save();
            $w->refresh();
        } else {
            $last = GameTask::last($w->id);
            $last->next_task = $e->id;
            $last->save();
            $last->refresh();
        }
        Watchdog::watch($request, '新增學習任務：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        if (isset($last)) {
            return response()->json([ 'last' => $last, 'task' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([ 'worksheet' => $w, 'task' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
    }

    public function task_update(Request $request)
    {
        $e = GameTask::find($request->input('tid'));
        if ($request->has('title')) $e->title = $request->input('title');
        if ($request->has('story')) $e->story = $request->input('story');
        if ($request->has('task')) $e->task = $request->input('task');
        if ($request->has('review')) $e->review = ($request->input('task') == 'yes');
        if ($request->has('xp')) $e->reward_xp = $request->input('xp');
        if ($request->has('gp')) $e->reward_gp = $request->input('gp');
        if ($request->has('item')) $e->reward_item = $request->input('item');
        $e->save();
        Watchdog::watch($request, '修改學習任務：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json([ 'task' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function task_remove(Request $request)
    {
        $e = GameTask::find($request->input('tid'));
        $w = $e->worksheet;
        if ($w->next_task == $e->id) {
            $w->next_task = $e->next_task;
            $w->save();
        }
        Watchdog::watch($request, '刪除學習任務：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $e->delete();
        return response()->json([ 'success' => $request->input('tid') ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function task_moveto(Request $request)
    {
        $e = GameTask::find($request->input('tid'));
        if ($e) {
            if ($request->has('x')) $e->coordinate_x = $request->input('x');
            if ($request->has('y')) $e->coordinate_y = $request->input('y');
            $e->save();    
        }
        Watchdog::watch($request, '學習任務移動到新的地圖座標：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json([ 'task' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function worksheet_assign($worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $worksheet = GameWorksheet::find($worksheet_id);
            $adventures = GameAdventure::findByWorksheet($worksheet_id);
            return view('game.worksheet_assign', [ 'teacher' => $teacher, 'worksheet' => $worksheet, 'adventures' => $adventures ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function adventure_add($worksheet_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $worksheet = GameWorksheet::find($worksheet_id);
            return view('game.adventure_add', [ 'teacher' => $teacher, 'worksheet' => $worksheet ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function adventure_insert(Request $request, $worksheet_id)
    {
        $user = Auth::user();
        $w = GameWorksheet::find($worksheet_id);
        if ($user->user_type == 'Teacher') {
            if (is_string($request->input('classrooms'))) {
                $classes[] = $request->input('classrooms');
            } else {
                $classes = $request->input('classrooms');
            }
            foreach ($classes as $cls) {
                $count = 0;
                $adventures = GameAdventure::findByClassroom($cls);
                if ($adventures) $count = $adventures->count();
                if ($count > 0) {
                    $open = false;
                } else {
                    $open = true;
                }
                $e = GameAdventure::create([
                    'syear' => current_year(),
                    'uuid' => $user->uuid,
                    'classroom_id' => $cls,
                    'worksheet_id' => $worksheet_id,
                    'open' => $open,
                ]);
                Watchdog::watch($request, '新增地圖探險：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));    
            }
            return redirect()->route('game.worksheet_assign', [ 'worksheet_id' => $worksheet_id ])->with('success', '學習單'.$w->title.'指派完成!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function adventure_switch(Request $request, $adventure_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameAdventure::find($adventure_id);
            $count = GameAdventure::findByClassroom($e->classroom_id)->count();
            if ($count > 0 && $request->input('open')) {
                return redirect()->route('game.worksheet_assign', [ 'worksheet_id' => $e->worksheet_id ])->with('error', '該班級已經在其它地圖中進行探險，因此無法開啟探險任務!');
            } else {
                if ($request->input('open')) {
                    $e->open = true;
                } else {
                    $e->open = false;
                }
                $e->save();
            }
            if ($e->open) {
                Watchdog::watch($request, '開啟地圖探險：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                return redirect()->route('game.worksheet_assign', [ 'worksheet_id' => $e->worksheet_id ])->with('success', '地圖探險已經開啟!');
            } else {
                Watchdog::watch($request, '關閉地圖探險：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));                
                return redirect()->route('game.worksheet_assign', [ 'worksheet_id' => $e->worksheet_id ])->with('success', '地圖探險已經關閉!');
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function adventure_remove(Request $request, $adventure_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameAdventure::find($adventure_id);
            $wid = $e->worksheet->id;
            Watchdog::watch($request, '刪除地圖探險：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $e->delete();
            return redirect()->route('game.worksheet_assign', [ 'worksheet_id' => $wid ])->with('success', '地圖探險已經刪除!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function adventures()
    {
        $room_id = session('gameclass');
        if (!$room_id) $room_id = session('viewclass');
        $classroom = Classroom::find($room_id);
        $characters = GameCharacter::findByClass($room_id);
        $adventure = GameAdventure::findByClassroom($room_id);
        $items = GameItem::all();
        return view('game.adventures', [ 'classroom' => $classroom, 'adventure' => $adventure, 'characters' => $characters, 'items' => $items]);
    }

    public function get_processes(Request $request)
    {
        $room_id = session('gameclass');
        if (!$room_id) $room_id = session('viewclass');
        $adventure_id = $request->input('aid');
        $task_id = $request->input('tid');
        $processes = GameProcess::findByClassroom($room_id, $adventure_id, $task_id);
        return response()->json([ 'processes' => $processes ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function process_comments(Request $request)
    {
        $teacher = Teacher::find(Auth::user()->uuid);
        $p = GameProcess::find($request->input('pid'));
        $p->comments = $request->input('comments');
        $p->save();
        broadcast(new GameCharacterChannel($p->character->stdno, 'task_comments:'.$p->toJson(JSON_UNESCAPED_UNICODE)));
        return response()->json([ 'process' => $p ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function process_overrule(Request $request)
    {
        $p = GameProcess::find($request->input('pid'));
        if ($request->input('notice') == 'yes') {
            $p->noticed = true;
            broadcast(new GameCharacterChannel($p->character->stdno, 'task_notice:'.$p->toJson(JSON_UNESCAPED_UNICODE)));
        } else {
            $p->noticed = false;
        }
        $p->save();
        return response()->json([ 'process' => $p ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function process_pass(Request $request)
    {
        $p = GameProcess::find($request->input('pid'));
        if ($request->input('pass') == 'yes') {
            if ($p->task->reward_xp) {
                $p->character->xp += $p->task->reward_xp;
            }
            if ($p->task->reward_gp) {
                $p->character->gp += $p->task->reward_gp;
            }
            $p->character->save();
            if ($p->task->reward_item) {
                $p->character->get_item($p->task->reward_item);
            }
            $p->reviewed_at = Carbon::now();
            $p->save();
            broadcast(new GameCharacterChannel($p->character->stdno, 'task_pass:'.$p->toJson(JSON_UNESCAPED_UNICODE)));
        } else {
            if ($p->task->reward_xp) {
                $p->character->xp -= $p->task->reward_xp;
            }
            if ($p->task->reward_gp) {
                $p->character->gp -= $p->task->reward_gp;
            }
            $p->character->save();
            if ($p->task->reward_item) {
                $p->character->loss_item($p->task->reward_item);
            }
            $p->reviewed_at = null;
            $p->save();
            broadcast(new GameCharacterChannel($p->character->stdno, 'task_pass:'.$p->toJson(JSON_UNESCAPED_UNICODE)));
        }
        return response()->json([ 'process' => $p ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

}
