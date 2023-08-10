<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Club;
use App\Models\ClubSection;
use App\Models\ClubKind;
use App\Models\ClubEnroll;
use App\Models\Unit;
use App\Models\Classroom;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ClubNotification;
use App\Notifications\ClubEnrollNotification;
use App\Notifications\ClubEnrolledNotification;
use App\Imports\ClubImport;
use App\Exports\ClubExport;
use App\Exports\ClubCashExport;
use App\Exports\ClubClassExport;
use App\Exports\ClubEnrolledExport;
use App\Exports\ClubRollExport;
use App\Exports\ClubTimeExport;
use App\Models\Watchdog;
use Carbon\Carbon;

class ClubController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        $cash = $user->hasPermission('club.cash');
        return view('app.club', ['manager' => ($user->is_admin || $manager), 'cash_reporter' => ($user->is_admin || $cash)]);
    }

    public function kindList()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $kinds = ClubKind::orderBy('weight')->get();
            return view('app.club_kinds', ['kinds' => $kinds]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function kindAdd()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            return view('app.club_addkind');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function kindInsert(Request $request)
    {
        $max = ClubKind::max('weight') + 1;
        $k = ClubKind::create([
            'name' => $request->input('title'),
            'single' => $request->boolean('single'),
            'stop_enroll' => $request->boolean('stop'),
            'manual_auditing' => $request->boolean('auditing'),
            'enrollDate' => $request->input('enroll'),
            'expireDate' => $request->input('expire'),
            'workTime' => $request->input('work'),
            'restTime' => $request->input('rest'),
            'style' => $request->input('style'),
            'weight' => $max,
        ]);
        Watchdog::watch($request, '新增社團類別：' . $k->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $kinds = ClubKind::orderBy('weight')->get();
        return view('app.club_kinds', ['kinds' => $kinds])->with('success', '社團類別已經新增完成！');
    }

    public function kindEdit($kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            return view('app.club_editkind', ['kind' => ClubKind::find($kid)]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function kindUpdate(Request $request, $kid)
    {
        $k = ClubKind::find($kid);
        $k->update([
            'name' => $request->input('title'),
            'single' => $request->boolean('single'),
            'stop_enroll' => $request->boolean('stop'),
            'manual_auditing' => $request->boolean('auditing'),
            'enrollDate' => $request->input('enroll'),
            'expireDate' => $request->input('expire'),
            'workTime' => $request->input('work'),
            'restTime' => $request->input('rest'),
            'style' => $request->input('style'),
        ]);
        Watchdog::watch($request, '更新社團類別：' . $k->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $kinds = ClubKind::orderBy('weight')->get();
        return view('app.club_kinds', ['kinds' => $kinds])->with('success', '社團類別已經修改完成！');
    }

    public function kindRemove(Request $request, $kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $k = ClubKind::find($kid);
            Watchdog::watch($request, '新增社團類別：' . $k->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $k->delete();
            $kinds = ClubKind::orderBy('weight')->get();
            return view('app.club_kinds', ['kinds' => $kinds])->with('success', '社團類別已經移除！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function kindUp(Request $request, $kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $kind = ClubKind::find($kid);
            $w = $kind->weight;
            if ($w > 1) {
                ClubKind::where('weight', $w - 1)->update(['weight' => $w]);
                $kind->weight = $w - 1;
                $kind->save();
            }
            Watchdog::watch($request, '修改社團類別的權重：' . $kind->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('clubs.kinds');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function kindDown(Request $request, $kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $max = ClubKind::max('weight');
            $kind = ClubKind::find($kid);
            $w = $kind->weight;
            if ($w < $max) {
                ClubKind::where('weight', $w + 1)->update(['weight' => $w]);
                $kind->weight = $w + 1;
                $kind->save();
            }
            Watchdog::watch($request, '修改社團類別的權重：' . $kind->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('clubs.kinds');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubList($kid = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            if ($kid) {
                $kind = ClubKind::find($kid);
                if (!$kind) return back()->with('error', '查無此類別！');
            } else {
                $kind = ClubKind::first();
            }
            $kinds = ClubKind::orderBy('weight')->get();
            $clubs = Club::where('kind_id', $kind->id)->get();
            return view('app.clubs', ['kind' => $kind, 'kinds' => $kinds, 'clubs' => $clubs]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubUpload($kid = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            if ($kid) {
                $kind = ClubKind::find($kid)->id;
            } else {
                $kind = ClubKind::first()->id;
            }
            $kinds = ClubKind::orderBy('weight')->get();
            return view('app.club_upload', ['kind' => $kind, 'kinds' => $kinds]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubImport(Request $request, $kid = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $kid = $request->input('kind');
            $importer = new ClubImport($kid);
            $importer->import($request->file('excel'));
            Watchdog::watch($request, '匯入學生社團：' . $request->file('excel')->path());
            return redirect()->route('clubs.admin', ['kid' => $kid])->with('success', '課外社團已經匯入完成！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubExport($kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = ClubKind::find($kid)->name;
            $exporter = new ClubExport($kid);
            return $exporter->download("$filename.xlsx");
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubRepetition($kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $uuids = ClubEnroll::repetition();
            $students = [];
            foreach ($uuids as $uuid) {
                $students[] = Student::find($uuid)->first();
            }
            return view('app.club_repetition', ['kind' => $kid, 'students' => $students]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubExportCash()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = '學生社團收費統計表';
            $exporter = new ClubCashExport();
            return $exporter->download("$filename.xlsx");
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubClassroom($kid, $class_id = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $classes = Classroom::all();
            if (!$class_id) $class_id = $classes->first()->id;
            $enrolls = ClubEnroll::currentByClass($class_id)->groupBy('uuid');
            return view('app.club_classroom', ['kind_id' => $kid, 'class_id' => $class_id, 'classes' => $classes, 'enrolls' => $enrolls]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubExportClass($kid, $class_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = Classroom::find($class_id)->name.'學生社團錄取名冊';
            $exporter = new ClubClassExport($class_id);
            return $exporter->download($filename);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubAdd($kid = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            if ($user->user_type == 'Teacher') {
                $teacher = $user->profile;
                $unit = $teacher->mainunit->id;
            } else {
                $unit = 0;
            }
            $kinds = ClubKind::orderBy('weight')->get();
            $units = Unit::main();
            return view('app.club_add', ['kind' => $kid, 'kinds' => $kinds, 'unit' => $unit, 'units' => $units]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubInsert(Request $request, $kid = null)
    {
        $kind_id =$request->input('kind');
        $title = $request->input('title');
        $found = Club::where('name', $title)->first();
        if ($found) {
            return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('error', '該課外社團已經存在，無法再新增！');
        }
        $grades = $request->input('grades');
        foreach ($grades as $k => $g) {
            $grades[$k] = (integer) $g;
        }
        $c = Club::create([
            'name' => $title,
            'short_name' => $request->input('short'),
            'kind_id' => $kind_id,
            'unit_id' => $request->input('unit'),
            'for_grade' => $grades ?: [],
            'self_remove' => $request->has('remove') ? true : false,
            'has_lunch' => $request->has('lunch') ? true : false,
            'devide' => $request->has('devide') ? true : false,
            'stop_enroll' => $request->has('stop') ? true : false,
        ]);
        Watchdog::watch($request, '新增學生社團：' . $c->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('success', '課外社團已經新增完成！');
    }

    public function clubEdit($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $kinds = ClubKind::orderBy('weight')->get();
            $units = Unit::main();
            return view('app.club_edit', ['kinds' => $kinds, 'units' => $units, 'club' => $club]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubUpdate(Request $request, $club_id)
    {
        $club = Club::find($club_id);
        $kind_id =$club->kind_id;
        $grades = $request->input('grades');
        foreach ($grades as $k => $g) {
            $grades[$k] = (integer) $g;
        }
        $club->update([
            'name' => $request->input('title'),
            'short_name' => $request->input('short'),
            'kind_id' => $request->input('kind'),
            'unit_id' => $request->input('unit'),
            'for_grade' => $grades ?: [],
            'self_defined' => $request->has('selfdefine') ? true : false,
            'self_remove' => $request->has('remove') ? true : false,
            'has_lunch' => $request->has('lunch') ? true : false,
            'devide' => $request->has('devide') ? true : false,
            'stop_enroll' => $request->has('stop') ? true : false,
        ]);
        Watchdog::watch($request, '更新學生社團：' . $club->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('success', '課外社團已經修改完成！');
    }

    public function clubRemove(Request $request, $club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $kind_id = $club->kind_id;
            if ($club->enrolls->isNotEmpty()) {
                return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('error', '此課外社團已經錄取學生，因此無法移除！');
            } else {
                Watchdog::watch($request, '移除學生社團：' . $club->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $club->delete();
                return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('success', '課外社團已經移除完成！');
            }
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubMail($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $enrolls = $club->section_enrolls();
            return view('app.club_mail', ['club' => $club, 'enrolls' => $enrolls]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubNotify(Request $request, $club_id)
    {
        $club = Club::find($club_id);
        $kind_id = $club->kind_id;
        $enroll_ids = $request->input('enrolls');
        $message = $request->input('message');
        if (!empty($enroll_ids)) {
            $enrolls = ClubEnroll::whereIn('id', $enroll_ids)->whereNotNull('email')->get();
            Notification::sendNow($enrolls, new ClubNotification($message));
            Watchdog::watch($request, '寄送郵件給學生社團：' . $club->name . '的錄取學生，郵件內容：' . $message);
            return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('success', '已安排於背景進行郵寄作業，郵件將會為您陸續寄出！');
        }
        return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('message', '因為沒有寄送對象，已經取消郵寄作業！');
    }

    public function clubPrune(Request $request, $club_id, $section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            if (!$section) $section = current_section();
            ClubEnroll::where('club_id', $club_id)->where('section', $section)->delete();
            $kind_id = $club->kind_id;
            $str = substr($section, 0, -1) . '學年'. ((substr($section, -1) == 1) ? '上' : '下') .'學期';
            Watchdog::watch($request, '移除學生社團' . $club->name . $str . '所有報名資訊');
            return redirect()->route('clubs.admin', ['kid' => $kind_id])->with('success', '已經移除此課外社團' . $str . '報名資訊，可以重新開始報名！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function sectionList($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $sections = ClubSection::where('club_id', $club_id)->get();
            $new = ClubSection::where('club_id', $club_id)->where('section', current_section())->exists();
            return view('app.club_sections', ['club' => $club, 'sections' => $sections, 'new' => $new]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function sectionAdd($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            return view('app.club_addsection', ['club' => $club]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function sectionInsert(Request $request, $club_id)
    {
        $found = ClubSection::where('club_id', $club_id)->where('section', current_section())->first();
        if ($found) {
            return redirect()->route('clubs.sections', ['club_id' => $club_id])->with('error', '本學期已經開班！');
        }
        $weekdays = $request->input('weekdays');
        foreach ($weekdays as $k => $w) {
            $weekdays[$k] = (integer) $w;
        }
        $c = ClubSection::create([
            'section' => current_section(),
            'club_id' => $club_id,
            'weekdays' => $weekdays,
            'self_defined' => $request->has('selfdefine') ? true : false,
            'startDate' => $request->input('startdate'),
            'endDate' => $request->input('enddate'),
            'startTime' => $request->input('starttime'),
            'endTime' => $request->input('endtime'),
            'teacher' => $request->input('teacher'),
            'location' => $request->input('location'),
            'memo' => $request->input('memo'),
            'cash' => $request->input('cash') ?: 0,
            'total' => $request->input('total') ?: 0,
            'maximum' => $request->input('limit') ?: 0,
        ]);
        Watchdog::watch($request, '新增學生社團開班資訊：' . $c->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('clubs.sections', ['club_id' => $club_id])->with('success', '開班資訊已經新增完成！');
    }

    public function sectionEdit($section_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $section = ClubSection::find($section_id);
            return view('app.club_editsection', ['section' => $section]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function sectionUpdate(Request $request, $section_id)
    {
        $section = ClubSection::find($section_id);
        $weekdays = $request->input('weekdays');
        if (!empty($weekdays)) {
            foreach ($weekdays as $k => $w) {
                $weekdays[$k] = (integer) $w;
            }    
        }
        $section->update([
            'weekdays' => $weekdays,
            'self_defined' => $request->has('selfdefine') ? true : false,
            'startDate' => $request->input('startdate'),
            'endDate' => $request->input('enddate'),
            'startTime' => $request->input('starttime'),
            'endTime' => $request->input('endtime'),
            'teacher' => $request->input('teacher'),
            'location' => $request->input('location'),
            'memo' => $request->input('memo'),
            'cash' => $request->input('cash') ?: 0,
            'total' => $request->input('total') ?: 0,
            'maximum' => $request->input('limit') ?: 0,
        ]);
        Watchdog::watch($request, '更新開班資訊：' . $section->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('clubs.sections', ['club_id' => $section->club_id])->with('success', '開班資訊已經修改完成！');
    }

    public function sectionRemove(Request $request, $section_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $section = ClubSection::find($section_id);
            if ($section->club->enrolls) {
                return redirect()->route('clubs.sections', ['club_id' => $section->club_id])->with('error', '此學期已經錄取學生，因此無法移除！');
            } else {
                Watchdog::watch($request, '移除開班資訊：' . $section->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $section->delete();
                return redirect()->route('clubs.sections', ['club_id' => $section->club_id])->with('success', '開班資訊已經移除完成！');
            }
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function clubEnroll()
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法報名參加學生社團！');
        $student = Student::find($user->uuid);
        $grade = substr($student->class_id, 0, 1);
        $clubs = Club::can_enroll($grade);
        return view('app.club_enroll', ['clubs' => $clubs, 'student' => $student]);
    }

    public function enrollAdd($club_id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') return redirect()->route('home')->with('error', '您不是學生，因此無法報名參加學生社團！');
        $club = Club::find($club_id);
        $student = Student::find($user->uuid);
        return view('app.club_addenroll', ['club' => $club, 'student' => $student]);
    }

    public function enrollInsert(Request $request, $club_id)
    {
        $user = Auth::user();
        $student = Student::find($user->uuid);
        if ($student->has_enroll($club_id)) {
            return redirect()->route('clubs.enroll')->with('error', '您已經報名該社團，無法再次報名！');
        }
        $club = Club::find($club_id);
        if ($club->kind->single) {
            $same_kind = $student->current_enrolls_for_kind($club->kind_id);
            if ($same_kind->isNotEmpty()) return redirect()->route('clubs.enroll')->with('error', '很抱歉，'.$club->kind->name.'只允許報名參加一個社團！');
        }
        $order = $club->count_enrolls() + 1;
        if ($club->section()->maximum != 0 && $order > $club->section()->maximum) {
            return redirect()->route('clubs.enroll')->with('error', '很抱歉，該學生社團已經額滿！');
        }
        $enrolls = Student::find($user->uuid)->section_enrolls();
        $weekdays = [];
        if ($club->section()->self_defined && $request->has('weekdays')) {
            $weekdays = $request->input('weekdays');
            foreach ($weekdays as $k => $w) {
                $weekdays[$k] = (integer) $w;
            }
        }
        $conflict = false;
        foreach ($enrolls as $en) {
            $conflict = $en->conflict($club, $weekdays);
            if ($conflict) break;
        }
        if ($conflict) return redirect()->route('clubs.enroll')->with('error', '很抱歉，此社團與其他已報名的社團上課時段重疊，因此無法報名！');
        $enroll = ClubEnroll::create([
            'uuid' => $user->uuid,
            'club_id' => $club_id,
            'need_lunch' => $request->input('lunch') ?: 0,
            'weekdays' => $weekdays,
            'identity' => $request->input('identity'),
            'parent' => $request->input('parent'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
        ]);
        Notification::sendNow($enroll, new ClubEnrollNotification($order));
        if ($club->kind->manual_auditin) {
            Watchdog::watch($request, '報名學生社團：' . $club->name . '，報名資訊：' . $enroll->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '報名順位：' . $order);
            return redirect()->route('clubs.enroll')->with('success', '您已經完成報名手續，報名順位為'.$order.'因須進行資格審核，待錄取作業完成後，將另行公告通知！');
        }
        $enroll->accepted = true;
        $enroll->save();
        $message = '';
        if ($order > $club->section()->total) $message = '，目前列為候補，若能遞補錄取將會另行通知！';
        Watchdog::watch($request, '報名學生社團：' . $club->name . '，報名資訊：' . $enroll->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '報名順位：' . $order . $message);
        return redirect()->route('clubs.enroll')->with('success', '您已經完成報名手續，報名順位為'.$order.$message);
    }

    public function enrollEdit($enroll_id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') {
            return redirect()->route('home')->with('error', '您不是學生，因此無法修改報名資訊！');
        }
        $enroll = ClubEnroll::find($enroll_id);
        if (!$enroll) return back()->with('error', '此報名紀錄已經不存在，請重整頁面後再試一次！');
        if ($enroll->uuid != $user->uuid) {
            return redirect()->route('home')->with('error', '這不是您的報名紀錄，因此無法修改！');
        }
        return view('app.club_editenroll', ['club' => $enroll->club, 'enroll' => $enroll]);
    }

    public function enrollUpdate(Request $request, $enroll_id)
    {
        $user = Auth::user();
        $enroll = ClubEnroll::find($enroll_id);
        if (!$enroll) {
            return redirect()->route('clubs.enroll')->with('error', '您要修改的報名紀錄，已經不存在！');
        }
        if ($enroll->uuid != $user->uuid) {
            return redirect()->route('home')->with('error', '這不是您的報名紀錄，因此無法修改！');
        }
        $weekdays = [];
        if ($enroll->club_section()->self_defined && $request->has('weekdays')) {
            $weekdays = $request->input('weekdays');
            foreach ($weekdays as $k => $w) {
                $weekdays[$k] = (integer) $w;
            }
        }
        $enroll->update([
            'need_lunch' => $request->input('lunch') ?: 0,
            'weekdays' => $weekdays,
            'identity' => $request->input('identity'),
            'parent' => $request->input('parent'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
        ]);
        Watchdog::watch($request, '修改報名資訊：' . $enroll->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('clubs.enroll')->with('success', '報名資訊已更新！');
    }

    public function enrollRemove(Request $request, $enroll_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $e = ClubEnroll::find($enroll_id);
            if ($e) {
                Watchdog::watch($request, '刪除報名資訊：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $e->delete();
                return back()->with('success', '報名資訊已經刪除！');
            }
        } else {
            $enroll = ClubEnroll::find($enroll_id);
            if (!$enroll) {
                return back()->with('error', '找不到您的報名紀錄，因此無法移除！');
            } 
            if ($enroll->uuid != $user->uuid) {
                return back()->with('error', '這不是您的報名紀錄，因此無法移除！');
            }
            Watchdog::watch($request, '取消報名：' . $enroll->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $enroll->delete();
            return back()->with('success', '已為您取消報名！');
        }
        return back();
    }

    public function enrollList(Request $request, $club_id, $section = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            if (!$club) {
                return redirect()->route('clubs.admin')->with('error', '找不到要管理的社團！');
            }
            $current = current_section();
            if (!$section) {
                if (!empty($club->sections)) {
                    $section_obj = $club->sections->first();
                    if ($section_obj) {
                        $section = $section_obj->section;
                    } else {
                        $section = $current;
                    }
                } else {
                    $section = $current;
                }
            }
            $order = $request->input('order');
            $groups = [];
            if ($club->devide) {
                $mygroup = $request->input('group'); 
                if (!$mygroup) $mygroup = 'all';
                $groups = $club->section_groups($section);
                if (in_array($mygroup, $groups)) {
                    if ($order && $order == 'stdno') {
                        $enrolls = $club->section_devide($mygroup, $section)->sortBy(function ($enroll) {
                            return $enroll->student->stdno;
                        });
                    } elseif ($order && $order != 'created_at') {
                        $enrolls = $club->section_devide($mygroup, $section)->sortBy($order);
                    } else {
                        $enrolls = $club->section_devide($mygroup, $section);
                    }
                }
            }
            if (!isset($mygroup) || $mygroup == 'all') {
                if ($order && $order == 'stdno') {
                    $enrolls = $club->section_enrolls($section)->sortBy(function ($enroll) {
                        return $enroll->student->stdno;
                    });
                } elseif ($order && $order != 'created_at') {
                    $enrolls = $club->section_enrolls($section)->sortBy($order);
                } else {
                    $enrolls = $club->section_enrolls($section);
                }
                $mygroup = 'all';
            }
            return view('app.club_enrolls', ['club' => $club, 'current' => $current, 'section' => $section, 'group' => $mygroup, 'groups' => $groups, 'enrolls' => $enrolls, 'order' => $order]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollValid(Request $request, $enroll_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $enroll = ClubEnroll::find($enroll_id);
            $enroll->update(['accepted' => true, 'audited_at' => Carbon::now()]);
            Watchdog::watch($request, '將報名資訊設定為錄取：' . $enroll->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->back()->with('success', '已錄取學生'.$enroll->student->realname.'！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollDeny(Request $request, $enroll_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $enroll = ClubEnroll::find($enroll_id);
            $enroll->update(['accepted' => false, 'audited_at' => Carbon::now()]);
            Watchdog::watch($request, '將報名資訊設定為不錄取：' . $enroll->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->back()->with('success', '已將學生'.$enroll->student->realname.'除名！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollDevide(Request $request, $club_id, $section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            if (!$club) {
                return redirect()->route('clubs.admin')->with('error', '找不到要管理的社團！');
            }
            $enrolls = $club->section_accepted($section)->sortBy(function (ClubEnroll $enroll) {
                return $enroll->student->stdno;
            });
            $all = $all_temp = $enrolls->count();
            if ($all == 0) {
                return redirect()->route('clubs.admin')->with('error', '尚未錄取學生，因此無法分組！');
            }
            $counter = [];
            $classes = [];
            foreach ($enrolls as $enroll) {
                $cls = $enroll->student->class_id;
                if (!isset($classes[$cls])) $classes[$cls] = 0;
                $classes[$cls] ++;
                if (!isset($counter[$cls])) $counter[$cls] = [];
                if (!isset($counter[$cls]['total'])) $counter[$cls]['total'] = 0;
                $counter[$cls]['total']++;
                if ($club->section($section)->self_defined) {
                    for ($i=1; $i<6; $i++) {
                        if (in_array($i, $enroll->weekdays)) {
                            if (!isset($counter[$cls]["w$i"])) $counter[$cls]["w$i"] = 0;
                            $counter[$cls]["w$i"]++;
                        } else {
                            if (!isset($counter[$cls]["w$i"])) $counter[$cls]["w$i"] = 0;
                        }
                    }
                }
            }
            if ($club->section($section)->self_defined) {
                foreach ($counter as $cls => $sumary) {
                    $max = 0;
                    for ($i=1; $i<6; $i++) {
                        if ($sumary["w$i"] > $max) $max = $sumary["w$i"];
                    }
                    $classes[$cls] = $max;
                }
            }
            $result = [];
            arsort($classes);
            $limit = $request->input('limit');
            if (empty($limit)) $limit = 20;
            $devide_num = $devide_temp = ceil($all / $limit);
            $mean = round($all / $devide_num);
            $n = 1;
            while (count($result) < $devide_num) {
                $solutions = find_solutions($classes, $mean);
                $last = 0;
                $d = $mean;
                foreach ($solutions as $k => $so) {
                    if (abs($mean - $so['sum']) < $d) {
                        $d = abs($mean - $so['sum']);
                        $last = $k;
                    }
                }
                $devide_classes = $solutions[$last]['classes'];
                foreach ($devide_classes as $cls) {
                    $counter[$cls]['group'] = $n;
                }
                $result[] = $solutions[$last];
                $classes = array_slice_assoc_inverse($classes, $solutions[$last]['classes']);
                $n ++;
            }
            if (count($classes) > 0) {
                $result[] = [ 'classes' => implode(',', $classes), 'sum' => array_sum($classes)  ];
            }
            return view('app.club_devide', ['club' => $club, 'section' => $section, 'all' => $all, 'limit' => $limit, 'devide_num' => $devide_num, 'counter' => $counter, 'result' => $result]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollConquer(Request $request, $club_id, $section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            if (!$club) {
                return redirect()->route('clubs.admin')->with('error', '找不到要管理的社團！');
            }
            $enrolls = $club->section_accepted($section)->sortBy(function (ClubEnroll $enroll) {
                return $enroll->student->stdno;
            });
            $classes = $request->input('classes');
            foreach ($enrolls as $enroll) {
                $cls = $enroll->student->class_id;
                $enroll->groupBy = $classes[$cls];
                $enroll->save();
            }
            Watchdog::watch($request, '將報名資訊自動分組：' . $enrolls->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->back()->with('success', '已將錄取學生自動分組！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollAppend($club_id, $class = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $classes = Classroom::all();
            if ($class) {
                $students = Classroom::find($class)->students;
            } else {
                $students = $classes->first()->students;
            }
            return view('app.club_appendenroll', ['club' => $club, 'current' => $class, 'classes' => $classes, 'students' => $students]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollInsertAppend(Request $request, $club_id, $class = null)
    {
        $uuid = $request->input('student');
        $student = Student::find($uuid);
        if ($student->has_enroll($club_id)) {
            return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('error', '該生已經報名此社團，無法再次報名！');
        }
        $club = Club::find($club_id);
        if ($club->kind->single) {
            $same_kind = $student->current_enrolls_for_kind($club->kind_id);
            if ($same_kind->isNotEmpty()) return redirect()->route('clubs.enroll')->with('error', '很抱歉，'.$club->kind->name.'只允許報名參加一個社團！');
        }
        $order = $club->count_enrolls() + 1;
        if ($club->section()->maximum != 0 && $order > $club->section()->maximum) {
            return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('error', '很抱歉，該學生社團已經額滿！');
        }
        $enrolls = Student::find($uuid)->section_enrolls();
        $weekdays = [];
        if ($club->section()->self_defined && $request->has('weekdays')) {
            $weekdays = $request->input('weekdays');
            foreach ($weekdays as $k => $w) {
                $weekdays[$k] = (integer) $w;
            }
        }
/*
        $conflict = false;
        foreach ($enrolls as $en) {
            $conflict = $en->conflict($club, $weekdays);
            if ($conflict) break;
        }
        if ($conflict) return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('error', '很抱歉，此社團與其他已報名的社團上課時段重疊，因此無法報名！');
*/
        $enroll = ClubEnroll::create([
            'uuid' => $uuid,
            'club_id' => $club_id,
            'need_lunch' => $request->input('lunch') ?: 0,
            'weekdays' => $weekdays,
            'identity' => $request->input('identity') ?: 0,
            'parent' => $request->input('parent'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
        ]);
        Notification::sendNow($enroll, new ClubEnrollNotification($order));
        if ($club->kind->manual_auditin) {
            Watchdog::watch($request, '新增報名資訊，學生社團：' . $club->name . '，學生：' . $student->class_id . $student->realname);
            return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('success', '已經完成報名手續，該生報名順位為'.$order.'！');
        }
        $enroll->accepted = true;
        $enroll->save();
        $message = '';
        if ($order > $club->section()->total) $message = '，目前列為候補，若能遞補錄取將會另行通知！';
        Watchdog::watch($request, '新增報名資訊，學生社團：' . $club->name . '，學生：' . $student->class_id . $student->realname);
        return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('success', '已經完成報名手續，該生報名順位為'.$order.$message);
    }

    public function enrollFastAppend($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            return view('app.club_fastappend', ['club' => $club]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollInsertFast(Request $request, $club_id)
    {
        $club = Club::find($club_id);
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
            if ($student->has_enroll($club_id)) {
                $message .= $stdno.$student->realname.'已經報名此社團，無法再次報名！';
                continue;
            }
            if ($club->kind->single) {
                $same_kind = $student->current_enrolls_for_kind($club->kind_id);
                if ($same_kind->isNotEmpty()) {
                    $message .= $stdno.$student->realname.'報名失敗，'.$club->kind->name.'只允許報名參加一個社團！';
                    continue;
                }
            }
            $order = $club->count_enrolls() + 1;
            if ($club->section()->maximum != 0 && $order > $club->section()->maximum) {
                $message .= $stdno.$student->realname.'因該社團已經額滿，無法報名！';
                continue;
            }
            $enrolls = $student->section_enrolls();
            $weekdays = [];
            if ($club->section()->self_defined && $request->has('weekdays')) {
                $weekdays = $request->input('weekdays');
                foreach ($weekdays as $k => $w) {
                    $weekdays[$k] = (integer) $w;
                }
            }
/*
            $conflict = false;
            foreach ($enrolls as $en) {
                $conflict = $en->conflict($club, $weekdays);
                if ($conflict) break;
            }
            if ($conflict) {
                $message .= $stdno.$student->realname.'，因上課時段重疊，因此無法報名！';
                continue;
            }
*/
            $enroll = ClubEnroll::create([
                'uuid' => $student->uuid,
                'club_id' => $club_id,
            ]);
            if ($club->kind->manual_auditin) {
                $message .= $stdno.$student->realname.'已經完成報名手續，報名順位為'.$order.'！';
                continue;
            }
            $enroll->accepted = true;
            $enroll->save();
            if ($order > $club->section()->total) {
                $message .= $stdno.$student->realname.'已經完成報名手續，報名順位為'.$order.'，目前列為候補！';
            } else {
                $message .= $stdno.$student->realname.'已經完成報名手續，報名順位為'.$order.'！';
            }
            Watchdog::watch($request, '快速新增報名資訊，學生社團：' . $club->name . '，學生：' . $stdno.$student->realname);
        }
        return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('success', $message);
    }

    public function enrollImport($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $current = current_section();
            $sections = ClubEnroll::sections();
            $sections = $sections->reject(function ($item) use ($current) {
                return $item->section == $current;
            });
            return view('app.club_import', ['club' => $club, 'current' => $current, 'sections' => $sections]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollImportOld(Request $request, $club_id)
    {
        $club = Club::find($club_id);
        $year = $request->input('year');
        $enrolls = $club->section_enrolls($year);
        foreach ($enrolls as $old) {
            $check = ClubEnroll::findBy($old->uuid, $club_id);
            if (!$check) {
                ClubEnroll::create([
                    'uuid' => $old->uuid,
                    'club_id' => $club_id,
                    'need_lunch' => $old->lunch ?: 0,
                    'weekdays' => $old->weekdays,
                    'identity' => $old->identity ?: 0,
                    'parent' => $old->parent,
                    'email' => $old->email,
                    'mobile' => $old->mobile,
                ]);
            }
            $student = $old->student;
            Watchdog::watch($request, '匯入舊生，學生社團：' . $club->name . '學生：' . $student->class_id . $student->realname);
        }
        return redirect()->route('clubs.enrolls', ['club_id' => $club_id])->with('success', '匯入完成！');
    }

    public function enrollNotify(Request $request, $club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $enrolled = $club->section_accepted()->filter(function ($enroll) {
                return !is_null($enroll->email);
            });
            Notification::send($enrolled, new ClubEnrolledNotification());
            Watchdog::watch($request, '寄送錄取通知，學生社團：' . $club->name . '報名資訊：' . $enrolled->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return back()->with('success', '已安排於背景進行錄取通知郵寄作業，郵件將會為您陸續寄出！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollExport($club_id, $section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = Club::find($club_id)->name.'錄取名冊';
            $exporter = new ClubEnrolledExport($club_id, $section);
            return $exporter->download($filename);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollExportRoll($club_id, $section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = Club::find($club_id)->name.'點名表';
            $exporter = new ClubRollExport($club_id, $section);
            return $exporter->download("$filename.xlsx");
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function enrollExportTime($club_id, $section)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = Club::find($club_id)->name.'時序表';
            $exporter = new ClubTimeExport($club_id, $section);
            return $exporter->download("$filename.xlsx");
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

}
