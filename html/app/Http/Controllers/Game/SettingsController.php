<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\GameSetting;
use App\Models\GameItem;
use App\Models\GameMonster;
use App\Models\GameEvaluate;
use App\Models\GameDungeon;
use App\Models\GameQuestion;
use App\Models\GameOption;
use App\Models\GameAnswer;
use App\Models\GameJourney;
use App\Models\Watchdog;

class SettingsController extends Controller
{

    public function positive()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $rules = GameSetting::positive($user->uuid);
            return view('game.positive', ['rules' => $rules]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function negative()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $rules = GameSetting::negative($user->uuid);
            return view('game.negative', ['rules' => $rules]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function positive_add()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $items = GameItem::all();
            return view('game.positive_add', [ 'items' => $items]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function negative_add()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            return view('game.negative_add');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            if ($request->input('type') == 'positive') {
                $sk = GameSetting::create([
                    'uuid' => $user->uuid,
                    'description' => $request->input('description'),
                    'type' => 'positive',
                ]);
            } else {
                $sk = GameSetting::create([
                    'uuid' => $user->uuid,
                    'description' => $request->input('description'),
                    'type' => 'negative',
                ]);
            }
            if ($request->has('xp')) $sk->effect_xp = $request->input('xp');
            if ($request->has('gp')) $sk->effect_gp = $request->input('gp');
            if ($request->has('item')) $sk->effect_item = $request->input('item');
            if ($request->has('hp')) $sk->effect_hp = $request->input('hp');
            if ($request->has('mp')) $sk->effect_mp = $request->input('mp');
            $sk->save();
            Watchdog::watch($request, '新增遊戲教室條款：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($request->input('type') == 'positive') {
                return redirect()->route('game.positive')->with('success', '已新增條款：'.$request->input('description').'！');
            } else {
                return redirect()->route('game.negative')->with('success', '已新增條款：'.$request->input('description').'！');
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function edit($rule_id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $rule = GameSetting::find($rule_id);
            if ($rule->type == 'positive') {
                $items = GameItem::all();
                return view('game.positive_edit', [ 'rule' => $rule, 'items' => $items ]);
            } else {
                return view('game.negative_edit', [ 'rule' => $rule ]);
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function update(Request $request, $rule_id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $sk = GameSetting::find($rule_id);
            $sk->uuid = $user->uuid;
            if ($request->has('description')) $sk->description = $request->input('description');
            if ($request->has('xp')) $sk->effect_xp = $request->input('xp');
            if ($request->has('gp')) $sk->effect_gp = $request->input('gp');
            if ($request->has('item')) $sk->effect_item = $request->input('item');
            if ($request->has('hp')) $sk->effect_hp = $request->input('hp');
            if ($request->has('mp')) $sk->effect_mp = $request->input('mp');
            $sk->save();
            Watchdog::watch($request, '修改遊戲教室條款：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if ($sk->type == 'positive') {
                return redirect()->route('game.positive')->with('success', '已修改條款：'.$request->input('description').'！');
            } else {
                return redirect()->route('game.negative')->with('success', '已修改條款：'.$request->input('description').'！');
            }
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function remove(Request $request, $rule_id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $sk = GameSetting::find($rule_id);
            $description = $sk->description;
            Watchdog::watch($request, '刪除遊戲教室條款：' . $sk->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $sk->delete();
            return redirect()->route('game.bases')->with('success', '已刪除條款：'.$description.'！');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluates()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $evaluates = GameEvaluate::findByUuid($user->uuid);
            return view('game.evaluates', [ 'evaluates' => $evaluates ]);
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function evaluate_add()
    {
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $e = GameEvaluate::find($evaluate_id);
            Watchdog::watch($request, '刪除遊戲評量：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $e->delete();
            GameQuestion::where('evaluate_id', $evaluate_id)->delete();
            GameOption::where('evaluate_id', $evaluate_id)->delete();
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
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $evaluate = GameEvaluate::find($evaluate_id);
            return view('game.evaluate_manage', [ 'evaluate' => $evaluate ]);
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $classes = $request->input('classrooms');
            foreach ($classes as $cls) {
                $e = GameDungeon::create([
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
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
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Teacher') {
            $e = GameDungeon::find($dungeon_id);
            $evaluate_id = $e->evaluate_id;
            $classroom_id = $e->classroom_id;
            Watchdog::watch($request, '刪除遊戲地下城：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $e->delete();
            $answers = GameAnswer::findBy($evaluate_id, $classroom_id);
            foreach ($answers as $a) {
                $a->journeys()->delete();
                $a->delete();
            }
            return redirect()->route('game.evaluate_assign', [ 'evaluate_id' => $evaluate_id ])->with('success', '地下城已經刪除!');
        } else {
            return redirect()->route('game')->with('error', '您沒有權限使用此功能！');
        }
    }

}
