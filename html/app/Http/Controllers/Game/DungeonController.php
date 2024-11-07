<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\GameMonster;
use App\Models\GameEvaluate;
use App\Models\GameDungeon;
use App\Models\GameQuestion;
use App\Models\GameOption;
use App\Models\GameAnswer;
use App\Models\GameJourney;
use App\Models\Watchdog;

class DungeonController extends Controller
{

    public function evaluates()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $evaluates = GameEvaluate::findByUuid($user->uuid);
            return view('game.evaluates', [ 'evaluates' => $evaluates ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_add()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $grades = Grade::all();
            return view('game.evaluate_add', [ 'teacher' => $teacher, 'grades' => $grades ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_insert(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameEvaluate::create([
                'uuid' => $user->uuid,
                'title' => $request->input('title'),
                'subject' => $request->input('subject'),
                'range' => $request->input('range'),
                'grade_id' => $request->input('grade'),
                'share' => $request->input('share') == 'yes',
            ]);
            Watchdog::watch($request, '新增遊戲評量：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.evaluates')->with('success', '評量'.$e->title.'新增成功!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_edit($evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $grades = Grade::all();
            $evaluate = GameEvaluate::find($evaluate_id);
            return view('game.evaluate_edit', [ 'teacher' => $teacher, 'evaluate' => $evaluate, 'grades' => $grades ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_update(Request $request, $evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameEvaluate::find($evaluate_id);
            $e->update([
                'title' => $request->input('title'),
                'subject' => $request->input('subject'),
                'range' => $request->input('range'),
                'grade_id' => $request->input('grade'),
                'share' => $request->input('share') == 'yes',
            ]);
            Watchdog::watch($request, '修改遊戲評量：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.evaluates')->with('success', '評量已經修改!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_remove(Request $request, $evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameEvaluate::find($evaluate_id);
            Watchdog::watch($request, '刪除遊戲評量：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $e->delete();
            GameDungeon::where('evaluate_id', $evaluate_id)->delete();
            GameAnswer::where('evaluate_id', $evaluate_id)->delete();
            GameJourney::where('evaluate_id', $evaluate_id)->delete();
            return redirect()->route('game.evaluates')->with('success', '評量已經刪除!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_manage($evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $evaluate = GameEvaluate::find($evaluate_id);
            return view('game.evaluate_manage', [ 'evaluate' => $evaluate ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_view($evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $evaluate = GameEvaluate::find($evaluate_id);
            return view('game.evaluate_view', [ 'evaluate' => $evaluate ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_duplicate($evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $old = GameEvaluate::find($evaluate_id);
            $new = $old->replicate();
            $new->uuid = $user->uuid;
            $new->share = false;
            $new->save();
            foreach ($old->questions as $q) {
                $newq = $q->replicate();
                $newq->evaluate_id = $new->id;
                $newq->save();
                foreach ($q->options as $o) {
                    $newo = $o->replicate();
                    $newo->evaluate_id = $new->id;
                    $newo->question_id = $newq->id;
                    $newo->save();
                    if ($o->id == $q->answer) {
                        $newq->answer = $newo->id;
                        $newq->save();
                    }
                }
            }
            return redirect()->route('game.evaluates')->with('success', '已為您複製評量！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function question_insert(Request $request)
    {
        $q = GameEvaluate::find($request->input('eid'))->max();
        if ($q) {
            $seq = $q->sequence;
            $seq ++;
        } else {
            $seq = 1;
        }
        $e = GameQuestion::create([
            'evaluate_id' => $request->input('eid'),
            'sequence' => $seq,
            'question' => $request->input('question'),
            'score' => $request->input('score'),
        ]);
        Watchdog::watch($request, '新增評量題目：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json([ 'id' => $e->id, 'question' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function question_update(Request $request)
    {
        $e = GameQuestion::find($request->input('qid'));
        $e->update([
            'question' => $request->input('question'),
            'score' => $request->input('score'),
        ]);
        Watchdog::watch($request, '修改評量題目：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json([ 'question' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function question_remove(Request $request)
    {
        $qid = $request->input('qid');
        $e = GameQuestion::find($qid);
        Watchdog::watch($request, '刪除評量題目：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $e->delete();
        GameOption::where('question_id', $qid)->delete();
        return response()->json([ 'success' => $request->input('qid') ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function question_answer(Request $request)
    {
        $e = GameQuestion::find($request->input('qid'));
        $e->answer = $request->input('oid');
        $e->save();
        Watchdog::watch($request, '設定評量題目的正確答案：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function option_insert(Request $request)
    {
        $q = GameQuestion::find($request->input('qid'));
        $o = $q->max();
        if ($o) {
            $seq = $o->sequence;
            $seq ++;
        } else {
            $seq = 1;
        }
        $e = GameOption::create([
            'evaluate_id' => $q->evaluate->id,
            'question_id' => $q->id,
            'sequence' => $seq,
            'option' => $request->input('option'),
        ]);
        Watchdog::watch($request, '新增評量選項：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json([ 'id' => $e->id, 'option' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function option_update(Request $request)
    {
        $e = GameOption::find($request->input('oid'));
        $e->update([
            'option' => $request->input('option'),
        ]);
        Watchdog::watch($request, '修改評量選項：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json([ 'option' => $e ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function option_remove(Request $request)
    {
        $e = GameOption::find($request->input('oid'));
        Watchdog::watch($request, '刪除評量選項：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $e->delete();
        return response()->json([ 'success' => $request->input('oid') ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function evaluate_assign($evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $evaluate = GameEvaluate::find($evaluate_id);
            $dungeons = GameDungeon::findByEvaluate($evaluate_id);
            return view('game.evaluate_assign', [ 'teacher' => $teacher, 'evaluate' => $evaluate, 'dungeons' => $dungeons ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function dungeon_add($evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $evaluate = GameEvaluate::find($evaluate_id);
            $monsters = GameMonster::all();
            return view('game.dungeon_add', [ 'teacher' => $teacher, 'evaluate' => $evaluate, 'monsters' => $monsters ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function dungeon_insert(Request $request, $evaluate_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            if (is_string($request->input('classrooms'))) {
                $classes[] = $request->input('classrooms');
            } else {
                $classes = $request->input('classrooms');
            }
            foreach ($classes as $cls) {
                $e = GameDungeon::create([
                    'syear' => current_year(),
                    'uuid' => $user->uuid,
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'classroom_id' => $cls,
                    'evaluate_id' => $evaluate_id,
                    'monster_id' => $request->input('monster'),
                    'times' => $request->input('times'),
                    'opened_at' => $request->input('open_date'),
                    'closed_at' => $request->input('close_date'),
                ]);
                Watchdog::watch($request, '新增遊戲地下城：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));    
            }
            return redirect()->route('game.evaluate_assign', [ 'evaluate_id' => $e->evaluate_id ])->with('success', '地下城'.$e->title.'新增成功!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function dungeon_edit($dungeon_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $teacher = Teacher::find($user->uuid);
            $dungeon = GameDungeon::find($dungeon_id);
            $monsters = GameMonster::all();
            return view('game.dungeon_edit', [ 'teacher' => $teacher, 'dungeon' => $dungeon, 'monsters' => $monsters ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function dungeon_update(Request $request, $dungeon_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameDungeon::find($dungeon_id);
            $e->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'monster_id' => $request->input('monster'),
                'times' => $request->input('times'),
                'opened_at' => $request->input('open_date'),
                'closed_at' => $request->input('close_date'),
            ]);
            Watchdog::watch($request, '修改遊戲地下城：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('game.evaluate_assign', [ 'evaluate_id' => $e->evaluate_id ])->with('success', '地下城已經修改!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function dungeon_remove(Request $request, $dungeon_id)
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $e = GameDungeon::find($dungeon_id);
            $evaluate_id = $e->evaluate_id;
            Watchdog::watch($request, '刪除遊戲地下城：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $e->delete();
            $answers = GameAnswer::findByDungeon($dungeon_id);
            foreach ($answers as $a) {
                $a->delete();
            }
            return redirect()->route('game.evaluate_assign', [ 'evaluate_id' => $evaluate_id ])->with('success', '地下城已經刪除!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function dungeons()
    {
        $dungeons = GameDungeon::findByClassroom(session('gameclass'));
        return view('game.dungeons', [ 'dungeons' => $dungeons ]);
    }

    public function answers($dungeon_id)
    {
        $answers = GameAnswer::findByDungeon($dungeon_id);
        return view('game.answers', [ 'answers' => $answers ]);
    }

    public function answer_remove($answer_id)
    {
        $answer = GameAnswer::find($answer_id);
        $dungeon_id = $answer->dungeon_id;
        $answer->delete();
        GameJourney::where('answer_id', $answer_id)->delete();
        return redirect()->route('game.answers', [ 'dungeon_id' => $dungeon_id ])->with([ 'success' => '答案卷已經刪除！' ]);
    }

    public function journeys($answer_id)
    {
        $answer = GameAnswer::find($answer_id);
        $journeys = GameJourney::findByAnswer($answer_id);
        return view('game.journeys', [ 'answer' => $answer, 'journeys' => $journeys ]);
    }

}
