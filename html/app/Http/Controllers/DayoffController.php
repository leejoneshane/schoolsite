<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Club;
use App\Models\Dayoff;
use App\Models\Roster;
use App\Exports\DayoffExport;
use App\Models\Watchdog;

class DayoffController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能管理公假單！');
        }
        $dayoffs = Dayoff::query()->paginate(16);
        return view('app.dayoff', ['reports' => $dayoffs]);
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能新增公假單！');
        }
        return view('app.dayoff_add');
    }

    public function insert(Request $request)
    {
        $datetimes = [];
        $dates = $request->input('dates');
        $from = $request->input('from');
        $to = $request->input('to');
        foreach ($dates as $k => $d) {
            $datetimes[] = (object) array('date' => $d, 'from' => $from[$k], 'to' => $to[$k]);
        }
        $dayoff = Dayoff::create([
            'uuid' => Auth::user()->uuid,
            'reason' => $request->input('reason'),
            'datetimes' => $datetimes,
            'location' => $request->input('location'),
            'who' => ($request->input('who') == 'yes') ? true : false,
            'memo' => $request->input('memo'),
        ]);
        Watchdog::watch($request, '新增公假單：' . $dayoff->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('dayoff')->with('success', '公假單新增完成！');
    }

    public function edit($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能修改公假單！');
        }
        $dayoff = Dayoff::find($id);
        return view('app.dayoff_edit', ['report' => $dayoff]);
    }

    public function update(Request $request, $id)
    {
        $datetimes = [];
        $dates = $request->input('dates');
        $from = $request->input('from');
        $to = $request->input('to');
        foreach ($dates as $k => $d) {
            $datetimes[] = (object) array('date' => $d, 'from' => $from[$k], 'to' => $to[$k]);
        }
        $dayoff = Dayoff::find($id);
        $dayoff->update([
            'uuid' => Auth::user()->uuid,
            'reason' => $request->input('reason'),
            'datetimes' => $datetimes,
            'location' => $request->input('location'),
            'who' => ($request->input('who') == 'yes') ? true : false,
            'memo' => $request->input('memo'),
        ]);
        Watchdog::watch($request, '修改公假單：' . $dayoff->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('dayoff')->with('success', '公假單修改完成！');
    }

    public function remove(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能移除公假單！');
        }
        $dayoff = Dayoff::find($id);
        if ($dayoff->count_students() > 0) {
            return redirect()->route('dayoff')->with('error', '公假單已經擁有學生名單，因此無法刪除！');  
        } else {
            Watchdog::watch($request, '移除公假單：' . $dayoff->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $dayoff->delete();
            return redirect()->route('dayoff')->with('success', '公假單移除完成！');    
        }
    }

    public function list($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能修改公假名單！');
        }
        $dayoff = Dayoff::find($id);
        $classes = Classroom::all();
        return view('app.dayoff_students', ['report' => $dayoff, 'classes' => $classes]);
    }

    public function classAdd($id, $class = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能勾選公假名單！');
        }
        if ($class) {
            $classroom = Classroom::find($class);
        } else {
            $teacher = Teacher::find($user->uuid);
            $classroom = Classroom::find($teacher->tutor_class);
        }
        $dayoff = Dayoff::find($id);
        $students = $dayoff->class_students($classroom->id);
        return view('app.dayoff_class', ['report' => $dayoff, 'classroom' => $classroom, 'students' => $students]);
    }

    public function classInsert(Request $request, $id, $class = null)
    {
        $dayoff = Dayoff::find($id);
        if ($class) {
            $class_id = $class;
        }
        $old = $dayoff->class_students($class_id);
        $students = $request->input('students');
        foreach ($students as $uuid) {
            $student = Student::find($uuid);
            DB::table('dayoff_students')->insert([
                'dayoff_id' => $id,
                'uuid' => $uuid,
            ]);
            Watchdog::watch($request, '新增學生「' . $student->stdno . $student->realname . '」到公假單「' . $dayoff->reason . '」中。');
            $old->reject(function ($stu) use ($uuid) {
                return $stu->uuid == $uuid;
            });
        }
        foreach ($old as $del) {
            $temp = DB::table('dayoff_students')
                ->where('dayoff_id', $id)
                ->where('uuid', $del->uuid)
                ->first();
            Watchdog::watch($request, '從表單「' . $dayoff->reason . '」移除學生「' . $del->stdno . $del->realname . '」。');
            DB::table('dayoff_students')->where('id', $temp->id)->delete();
        }
        return redirect()->route('dayoff.students', ['id' => $id])->with('success', '已為您修改和儲存公假名單！');
    }

    public function fastAdd($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能輸入公假名單！');
        }
        $dayoff = Dayoff::find($id);
        return view('app.dayoff_fast', ['report' => $dayoff]);
    }

    public function fastInsert(Request $request, $id)
    {
        $dayoff = Dayoff::find($id);
        $message = '';
        $regex = '/[\s\t\r\n]+/';
        $list = preg_replace($regex, ' ', $request->input('stdno'));
        $students = explode(' ', $list);
        foreach ($students as $stdno) {
            $stdno = trim($stdno);
            if (strlen($stdno) != 5) continue;
            $class_id = substr($stdno, 0, 3);
            $seat = (integer) substr($stdno, -2);
            $student = Student::findByStdno($class_id, $seat);
            if ($dayoff->student_occupy($student->uuid)) {
                $message .= $stdno.$student->realname.'已經在公假名單中，不用再輸入！';
                continue;
            }
            $record = DB::table('dayoff_students')->insertOrIgnore([
                'uuid' => $student->uuid,
                'dayoff_id' => $id,
            ]);
            if ($record) {
                $message .= $stdno.$student->realname.'已經新增到公假單中！';
                continue;
            }
            Watchdog::watch($request, '快速新增學生名單到公假單「' . $dayoff->reason . '」中，學生：' . $stdno.$student->realname);
        }
        return redirect()->route('dayoff.students', ['id' => $id])->with('success', $message);
    }

    public function importClub($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能輸入公假名單！');
        }
        $dayoff = Dayoff::find($id);
        $clubs = Club::all()->filter(function ($club) {
            return $club->count_accepted() > 0;
        });
        return view('app.dayoff_club', ['report' => $dayoff, 'clubs' => $clubs]);
    }

    public function importClubSave(Request $request, $id)
    {
        $message = '';
        $dayoff = Dayoff::find($id);
        $club = Club::find($request->input('club'));
        foreach ($club->accepted_students() as $stu) {
            if ($dayoff->student_occupy($stu->uuid)) {
                $message .= $stu->stdno.$stu->realname.'已經在公假名單中，不用再輸入！';
                continue;
            }
            $record = DB::table('dayoff_students')->insertOrIgnore([
                'uuid' => $stu->uuid,
                'dayoff_id' => $id,
            ]);
            if ($record) {
                $message .= $stu->stdno.$stu->realname.'已經新增到公假單中！';
                continue;
            }
            Watchdog::watch($request, '匯入學生課外社團錄取名單到公假單「' . $dayoff->reason . '」中，學生：' . $stu->stdno.$stu->realname);
        }
        return redirect()->route('dayoff.students', ['id' => $id])->with('success', $message);
    }

    public function importRoster($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if (!($user->is_admin || $manager)) {
            return redirect()->route('home')->with('error', '只有管理員才能輸入公假名單！');
        }
        $dayoff = Dayoff::find($id);
        $rosters = Roster::all()->filter(function ($roster) {
            return $roster->count() > 0;
        });
        return view('app.dayoff_roster', ['report' => $dayoff, 'rosters' => $rosters]);
    }

    public function importRosterSave(Request $request, $id)
    {
        $message = '';
        $dayoff = Dayoff::find($id);
        $roster = Roster::find($request->input('roster'));
        foreach ($roster->section_students() as $stu) {
            if ($dayoff->student_occupy($stu->uuid)) {
                $message .= $stu->stdno.$stu->realname.'已經在公假名單中，不用再輸入！';
                continue;
            }
            $record = DB::table('dayoff_students')->insertOrIgnore([
                'uuid' => $stu->uuid,
                'dayoff_id' => $id,
            ]);
            if ($record) {
                $message .= $stu->stdno.$stu->realname.'已經新增到公假單中！';
                continue;
            }
            Watchdog::watch($request, '匯入已填報學生名單到公假單「' . $dayoff->reason . '」中，學生：' . $stu->stdno.$stu->realname);
        }
        return redirect()->route('dayoff.students', ['id' => $id])->with('success', $message);
    }

    public function removeStudent(Request $request, $id)
    {
        $record = DB::table('dayoff_students')->where('id', $id)->first();
        $dayoff = Dayoff::find($record->dayoff_id);
        $student = Student::find($record->uuid);
        Watchdog::watch($request, '從公假單「' . $dayoff->reason . '」中移除學生「' . $student->stdno . $student->realname . '」');
        DB::table('dayoff_students')->where('id', $id)->delete();
        return redirect()->route('dayoff.students', ['id' => $dayoff->id])->with('success', '已為您移除'.$student->stdno.$student->realname.'！');
    }

    public function download($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if ($user->is_admin || $manager) {
            $filename = config('app.name').'公假單';
            $exporter = new DayoffExport($id);
            return $exporter->download($filename);
        } else {
            return redirect()->route('home')->with('error', '只有管理員才能下載公假單！');
        }
    }

    public function print($id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('dayoff.manager');
        if ($user->is_admin || $manager) {
            $filename = config('app.name').'公假單';
            $exporter = new DayoffExport($id);
            return $exporter->view($filename);
        } else {
            return redirect()->route('home')->with('error', '只有管理員才能列印公假單！');
        }
    }

}