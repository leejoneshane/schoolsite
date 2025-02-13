<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Seats;
use App\Models\SeatsTheme;
use App\Models\User;
use App\Models\Student;
use App\Models\Watchdog;

class SeatsController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user->user_type == 'Teacher') {
            $seats = Seats::findByUUID($user->uuid);
        } else {
            $stu = Student::find($user->uuid);
            $seats = Seats::findByClass($stu->class_id);
        }
        return view('app.seats', ['seats' => $seats]);
    }

    public function theme()
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') {
            return redirect()->route('home')->with('error', '只有教職員才能管理分組座位表！');
        }
        $manager = $user->is_admin || $user->hasPermission('seats.manager');
        $templates = SeatsTheme::all();
        return view('app.seats_theme', ['manager' => $manager, 'templates' => $templates]);
    }

    public function addTheme()
    {
        $styles = SeatsTheme::$styles;
        return view('app.seats_addtheme', ['styles' => $styles]);
    }

    public function insertTheme(Request $request)
    {
        $matrix = json_decode($request->input('matrix'));
        $theme = SeatsTheme::create([
            'name' => $request->input('title'),
            'matrix' => $matrix,
            'uuid' => $request->user()->uuid,
        ]);
        Watchdog::watch($request, '新增座位表版型：' . $theme->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('seats.theme')->with('success', '座位表版型新增完成！');
    }

    public function showTheme(Request $request)
    {
        $template = SeatsTheme::find($request->input('id'));
        $styles = SeatsTheme::$styles;
        $view = view('app.seats_viewtheme', ['template' => $template, 'styles' => $styles])->render();
        return response()->json((object) ['html' => $view]);
    }

    public function editTheme($id)
    {
        $template = SeatsTheme::find($id);
        $styles = SeatsTheme::$styles;
        return view('app.seats_edittheme', ['template' => $template, 'styles' => $styles]);
    }

    public function updateTheme(Request $request, $id)
    {
        $matrix = json_decode($request->input('matrix'));
        $theme = SeatsTheme::find($id);
        $theme->update([
            'name' => $request->input('title'),
            'matrix' => $matrix,
        ]);
        Watchdog::watch($request, '修改座位表版型：' . $theme->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('seats.theme')->with('success', '座位表版型修改完成！');
    }

    public function removeTheme(Request $request, $id)
    {
        $theme = SeatsTheme::find($id);
        if ($theme->seats->count() > 0) {
            return redirect()->route('seats.theme')->with('error', '這個版型正在使用中，因此無法刪除！');
        }
        Watchdog::watch($request, '移除座位表版型：' . $theme->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $theme->delete();
        return redirect()->route('seats.theme')->with('success', '座位表版型新增完成！');
    }

    public function add()
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') {
            return redirect()->route('home')->with('error', '只有教職員才能新增分組座位表！');
        }
        $themes = SeatsTheme::all();
        $classes = employee()->classrooms;
        return view('app.seats_add', ['classes' => $classes, 'themes' => $themes]);

    }

    public function insert(Request $request)
    {
        $seats = Seats::create([
            'class_id' => $request->input('classroom'),
            'theme_id' => $request->input('theme'),
            'uuid' => $request->user()->uuid,
        ]);
        Watchdog::watch($request, '新增座位表：' . $seats->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('seats')->with('success', '座位表新增完成！');
    }

    public function auto(Request $request, $id)
    {
        $user = Auth::user();
        $seats = Seats::find($id);
        if ($user->uuid != $seats->uuid) {
            return redirect()->route('home')->with('error', '這不是您建立的座位表，因此無法編輯！');
        }
        //清空座位表
        DB::table('seats_students')->where('seats_id', $id)->delete();
        //計算版型中每組座位數以及總座位數 seat
        $groups = [];
        $seat = 0;
        $matrix = $seats->theme->matrix;
        foreach ($matrix as $cols) {
            foreach ($cols as $group) {
                if ($group > 0) {
                    if (!isset($groups[$group-1])) {
                        $groups[$group-1] = 1;    
                    } else {
                        $groups[$group-1]++;
                    }
                    $seat++;
                }
            }
        }
        //取得此班級所有學生，並計算總人數 total
        $students = $seats->classroom->students;
        $total = $students->count();
        if ($total > $seat) {
            return redirect()->route('seats')->with('error', '班級人數太多，請變更為大一點的版型！');
        } elseif ($seat > $total) {
            //修正每組實際人數
            for ($i=0; $i<$seat-$total; $i++) {
                //找出人數最多的組，並將人數減一
                $max_num = max($groups);
                $key = array_search($max_num, $groups);
                $groups[$key]--;
            }
        }
        //篩選出男生並亂數排列
        $boys = $students->filter(function ($stu) {
            return $stu->gender == 1;
        })->shuffle();
        //篩選出女生並亂數排列
        $girls = $students->filter(function ($stu) {
            return $stu->gender != 1;
        })->shuffle();
        //將男生和女生間隔安插到 queue 中
        $queue = []; 
        while ($boys->count() > 0 || $girls->count() > 0) {
            if ($boys->count() > 0) {
                $queue[] = $boys->shift();
            }
            if ($girls->count() > 0) {
                $queue[] = $girls->shift();
            }
        }
        foreach ($groups as $g => $num) {
            for ($i=0; $i<$num; $i++) {
                $stu = array_shift($queue);
                DB::table('seats_students')->insert([
                    'seats_id' => $id,
                    'uuid' => $stu->uuid,
                    'sequence' => ($i+1),
                    'group_no' => ($g+1),
                ]);
                Watchdog::watch($request, $seats->name.'自動分組：第'.($g+1).'組'.$stu->seat.$stu->realname);
            }
        }
        return redirect()->route('seats')->with('success', '座位表已經由系統自動分配座位，請務必檢視調整！');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $seats = Seats::find($id);
        if ($user->uuid != $seats->uuid) {
            return redirect()->route('home')->with('error', '這不是您建立的座位表，因此無法變更！');
        }
        $styles = SeatsTheme::$styles;
        $students = [];
        foreach ($seats->classroom->students as $stu) {
            if ($stu->gender == 1) {
                $label = '<label class="text-blue-700">'.(($stu->seat >= 10) ? $stu->seat : '0'.$stu->seat).'　'.$stu->realname.'</label>';
            } else {
                $label = '<label class="text-red-700">'.(($stu->seat >= 10) ? $stu->seat : '0'.$stu->seat).'　'.$stu->realname.'</label>';
            }
            $obj = (object) [
                'uuid' => $stu->uuid,
                'html' => $label,
            ];
            $students[$stu->seat] = $obj;
        };
        $matrix = [];
        foreach ($seats->matrix() as $i => $cols) {
            foreach ($cols as $j => $pos) {
                if (is_object($pos->student) && isset($students[$pos->student->seat])) {
                    $matrix[$i][$j][0] = $pos->student->seat;
                    $matrix[$i][$j][1] = $students[$pos->student->seat]->html;
                    $matrix[$i][$j][2] = $pos->sequence;
                    $matrix[$i][$j][3] = $pos->group;
                } else {
                    $matrix[$i][$j][0] = 0;
                    $matrix[$i][$j][1] = '&nbsp;';
                    $matrix[$i][$j][2] = $pos->sequence;
                    $matrix[$i][$j][3] = $pos->group;
                }
            }
        }
        $without = [];
        $without[] = [ 0, '　清　除　' ];
        foreach ($seats->students_without() as $stu) {
            $without[] = [ $stu->seat, $students[$stu->seat]->html ];
        }
        return view('app.seats_edit', ['seats' => $seats, 'styles' => $styles, 'students' => $students, 'matrix' => $matrix, 'without' => $without]);
    }

    public function show($id)
    {
        $seats = Seats::find($id);
        if ($seats) {
            $styles = SeatsTheme::$styles;
            return view('app.seats_view', ['seats' => $seats, 'styles' => $styles]);    
        } else {
            return redirect()->route('seats')->with('success', '找不到分組座位表，因此無法查閱！');
        }
    }

    public function group($id)
    {
        $seats = Seats::find($id);
        $styles = SeatsTheme::$styles;
        $groups =[];
        foreach ($seats->students as $stu) {
            $groups[$stu->pivot->group_no][] = $stu; 
        }
        foreach ($seats->students_without() as $stu) {
            $groups['none'][] = $stu;
        }
        return view('app.seats_group', ['seats' => $seats, 'styles' => $styles, 'groups' => $groups]);
    }

    public function change($id)
    {
        $user = Auth::user();
        $seats = Seats::find($id);
        if ($user->uuid != $seats->uuid) {
            return redirect()->route('home')->with('error', '這不是您建立的座位表，因此無法變更！');
        }
        $themes = SeatsTheme::all();
        $classes = employee()->classrooms;
        return view('app.seats_change', ['seats' => $seats, 'classes' => $classes, 'themes' => $themes]);
    }

    public function updateChange(Request $request, $id)
    {
        $user = Auth::user();
        $seats = Seats::find($id);
        $old = $seats->class_id;
        $new = $request->input('classroom');
        $seats->update([
            'class_id' => $new,
            'theme_id' => $request->input('theme'),
            'uuid' => $user->uuid,
        ]);
        if ($old != $new) {
            DB::table('seats_students')->where('seats_id', $id)->delete();
        }
        Watchdog::watch($request, '修改座位表：' . $seats->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('seats')->with('success', '座位表版型已經變更！');
    }

    public function remove(Request $request, $id)
    {
        $user = Auth::user();
        $seats = Seats::find($id);
        if ($user->uuid != $seats->uuid) {
            return redirect()->route('home')->with('error', '這不是您建立的座位表，因此無法移除！');
        }
        DB::table('seats_students')->where('seats_id', $id)->delete();
        Watchdog::watch($request, '移除座位表：' . $seats->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $seats->delete();
        return redirect()->route('seats')->with('success', '座位表已經移除！');
    }

    public function assign(Request $request)
    {
        $id = $request->input('seats_id');
        $uuid = $request->input('uuid');
        $seq = $request->input('sequence');
        $g = $request->input('group_no');
        $seats = Seats::find($id);
        $stu = Student::find($uuid);
        DB::table('seats_students')->insert([
            'seats_id' => $id,
            'uuid' => $uuid,
            'sequence' => $seq,
            'group_no' => $g,
        ]);
        Watchdog::watch($request, $seats->name.'安排座位：第'.$g.'組'.$stu->seat.$stu->realname);
        return response()->json('success');
    }

    public function unassign(Request $request)
    {
        $id = $request->input('seats_id');
        $uuid = $request->input('uuid');
        $seats = Seats::find($id);
        $stu = Student::find($uuid);
        DB::table('seats_students')->where('seats_id', $id)->where('uuid', $uuid)->delete();
        Watchdog::watch($request, $seats->name.'取消'.$stu->seat.$stu->realname.'的座位！');
        return response()->json('success');
    }

}
