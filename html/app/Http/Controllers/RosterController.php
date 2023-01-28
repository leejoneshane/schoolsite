<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Watchdog;
use App\Models\Roster;
use Carbon\Carbon;

class RosterController extends Controller
{

    public function list($section = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        if ($section) {
            $section = current_section();
        }
        $teacher = Teacher::find($user->uuid);
        $rosters = Roster::all();
        $sections = Roster::sections();
        $classes = Classroom::orderBy('id')->get();
        return view('app.rosters', ['teacher' => $teacher, 'section' => $section, 'sections' => $sections, 'rosters' => $rosters, 'classes' => $classes]);
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('roster.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能新增學生表單！');
        }
        return view('app.rosteradd');
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
            'min' => $request->input('min'),
            'max' => $request->input('max'),
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
        return view('app.rosteredit', ['roster' => $roster]);
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
            'min' => $request->input('min'),
            'max' => $request->input('max'),
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

    public function summary($id, $section)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $roster = Roster::find($id);
        $records = DB::table('rosters_students')
            ->select('*', DB::raw('count(*) as total'))
            ->where('roster_id', $id)
            ->where('section', $section)
            ->groupBy('class_id')
            ->get();
        $sum = [];
        foreach ($records as $record) {
            $sum[$record->class_id] = $record->total;
        }
        return view('app.rostersummary', ['roster' => $roster, 'summary' => $sum]);
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
        return view('app.rosterenroll', ['roster' => $roster, 'classroom' => $classroom]);
    }

    public function save_enroll(Request $request, $id)
    {
        $roster = Roster::find($id);
        $class_id = $request->input('classroom');
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
            Watchdog::watch($request, '新增學生「' . $student->class_id . $student->seat . $student->realname . '」到表單「' . $roster->name . '」中。');
        }
        return redirect()->route('roster.enroll', ['id' => $id, 'class' => $class_id])->with('success', '已為您填報學生表單！');
    }

    public function show($id, $section)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $roster = Roster::find($id);
        $students = $roster->year_students($section);
        return view('app.rostershow', ['roster' => $roster, 'students' => $students]);
    }

    public function download($id, $section)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能填報學生名單！');
        }
        $roster = Roster::find($id);
        $students = $roster->year_students($section);
        return view('app.rostershow', ['roster' => $roster, 'students' => $students]);
    }

}
