<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Domain;
use App\Models\Watchdog;
use App\Models\Roster;
use App\Exports\RosterExport;
use Carbon\Carbon;

class RosterController extends Controller
{

    public function list($section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('roster.manager');
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $teacher = Teacher::find($user->uuid);
        $rosters = Roster::all();
        $current = current_section();
        if (!$section) {
            $section = $current;
        }
        $sections = Roster::sections();
        if (!in_array($current, $sections)) $sections[] = $current;
        return view('app.rosters', ['teacher' => $teacher, 'manager' => $user->is_admin || $manager, 'section' => $section, 'sections' => $sections, 'rosters' => $rosters]);
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('roster.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能新增學生表單！');
        }
        $grades = Grade::all();
        $fields = Roster::FIELDS;
        $domains = Domain::all();
        return view('app.rosteradd', ['grades' => $grades, 'fields' => $fields, 'domains' => $domains]);
    }

    public function insert(Request $request)
    {
        $roster = Roster::create([
            'name' => $request->input('title'),
            'grades' => $request->input('grades'),
            'fields' => $request->input('fields'),
            'domains' => $request->input('domains'),
            'started_at' => $request->input('start'),
            'ended_at' => $request->input('end'),
            'min' => min($request->input('min'), $request->input('max')),
            'max' => max($request->input('min'), $request->input('max')),
        ]);
        Watchdog::watch($request, '新增學生表單：' . $roster->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('rosters')->with('success', '學生表單新增完成！');
    }

    public function edit($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('roster.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能修改學生表單！');
        }
        $roster = Roster::find($id);
        $grades = Grade::all();
        $fields = Roster::FIELDS;
        $domains = Domain::all();
        return view('app.rosteredit', ['roster' => $roster, 'fields' => $fields, 'grades' => $grades, 'domains' => $domains]);
    }

    public function update(Request $request, $id)
    {
        $roster = Roster::find($id);
        $roster->update([
            'name' => $request->input('title'),
            'grades' => $request->input('grades'),
            'fields' => $request->input('fields'),
            'domains' => $request->input('domains'),
            'started_at' => $request->input('start'),
            'ended_at' => $request->input('end'),
            'min' => min($request->input('min'), $request->input('max')),
            'max' => max($request->input('min'), $request->input('max')),
        ]);
        Watchdog::watch($request, '更新學生表單：' . $roster->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('repair')->with('success', '學生表單編輯完成！');
    }

    public function remove(Request $request, $id)
    {
        $roster = Roster::find($id);
        Watchdog::watch($request, '移除學生表單：' . $roster->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        DB::table('rosters_students')->where('roster_id', $id)->delete();
        $roster->delete();
        return redirect()->route('rosters')->with('success', '學生表單已經移除！');
    }

    public function reset(Request $request, $id)
    {
        $roster = Roster::find($id);
        DB::table('rosters_students')
            ->where('roster_id', $id)
            ->where('section', current_section())
            ->delete();
        Watchdog::watch($request, '重設學生表單：' . $roster->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('rosters')->with('success', '學生表單已經重設！');
    }

    public function summary($id, $section = null)
    {
        if (!$section) $section = current_section();
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $roster = Roster::find($id);
        $records = DB::table('rosters_students')
            ->select('class_id', DB::raw('count(*) as total'))
            ->where('roster_id', $id)
            ->where('section', $section)
            ->groupBy('class_id')
            ->get();
        $classes = $roster->classes(); 
        $sum = [];
        foreach ($records as $record) {
            $sum[$record->class_id] = $record->total;
        }
        return view('app.rostersummary', ['roster' => $roster, 'section' => $section, 'classes' => $classes, 'summary' => $sum]);
    }

    public function enroll($id, $class = null)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        if ($class) {
            $classroom = Classroom::find($class);
        } else {
            $teacher = Teacher::find($user->uuid);
            $classroom = Classroom::find($teacher->tutor_class);
        }
        $roster = Roster::find($id);
        $fields = [];
        if ($roster->fields) {
            foreach ($roster->fields as $f1) {
                foreach (Roster::FIELDS as $f2) {
                    if ($f1 == $f2['id']) {
                        $fields[] = $f2;
                    }
                }
            }
        }
        $students = $roster->class_students($classroom->id);
        return view('app.rosterenroll', ['roster' => $roster, 'classroom' => $classroom, 'fields' => $fields, 'students' => $students]);
    }

    public function save_enroll(Request $request, $id, $class = null)
    {
        $roster = Roster::find($id);
        if ($class) {
            $class_id = $class;
        } else {
            $class_id = $request->input('classroom');
        }
        $old = $roster->class_students($class_id);
        $students = $request->input('students');
        foreach ($students as $uuid) {
            $student = Student::find($uuid);
            DB::table('rosters_students')->insert([
                'section' => current_section(),
                'class_id' => $class_id,
                'roster_id' => $id,
                'uuid' => $uuid,
                'deal' => $request->user()->uuid,
            ]);
            Watchdog::watch($request, '新增學生「' . $student->stdno . $student->realname . '」到表單「' . $roster->name . '」中。');
            $old->reject(function ($stu) use ($uuid) {
                return $stu->uuid == $uuid;
            });
        }
        foreach ($old as $del) {
            $temp = DB::table('rosters_students')
                ->where('section', current_section())
                ->where('roster_id', $id)
                ->where('uuid', $del->uuid)
                ->first();
            Watchdog::watch($request, '從表單「' . $roster->name . '」移除學生「' . $del->stdno . $del->realname . '」。');
            DB::table('rosters_students')->where('id', $temp->id)->delete();
        }
        return redirect()->route('roster.enroll', ['id' => $id, 'class' => $class_id])->with('success', '已為您填報學生表單！');
    }

    public function show(Request $request, $id, $section, $class = null)
    {
        $referer = $request->headers->get('referer');
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $roster = Roster::find($id);
        $fields = [];
        if ($roster->fields) {
            foreach ($roster->fields as $f1) {
                foreach (Roster::FIELDS as $f2) {
                    if ($f1 == $f2['id']) {
                        $fields[] = $f2;
                    }
                }
            }
        }
        if ($class) {
            $students = $roster->class_students($class, $section);
        } else {
            $students = $roster->year_students($section);
        }
        return view('app.rostershow', ['referer' => $referer, 'roster' => $roster, 'fields' => $fields, 'students' => $students]);
    }

    public function download($id, $section)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $filename = Roster::find($id)->name . '名單一覽表';
        $exporter = new RosterExport($id, $section);
        return $exporter->download("$filename.xlsx");
    }

}
