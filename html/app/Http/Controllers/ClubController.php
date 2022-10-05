<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Club;
use App\Models\ClubKind;
use App\Models\ClubEnroll;
use App\Models\Unit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ClubNotification;
use App\Notifications\ClubEnrollNotification;
use App\Notifications\ClubEnrolledNotification;
use App\Imports\ClubImport;
use App\Exports\ClubExport;
use App\Exports\ClubCashExport;

class ClubController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        $enroll = ClubEnroll::first();
        $club = $enroll->club;
        $student = $enroll->student;
        return view('emails.club', ['enroll' => $enroll, 'club' => $club, 'student' => $student, 'info' => $club->memo]);
        return view('app.club', ['manager' => ($user->is_admin || $manager)]);
    }

    public function kindList()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $kinds = ClubKind::orderBy('weight')->get();
            return view('app.clubkind', ['kinds' => $kinds]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function kindAdd()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            return view('app.clubaddkind');
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function kindInsert(Request $request)
    {
        $max = ClubKind::max('weight') + 1;
        ClubKind::create([
            'name' => $request->input('title'),
            'single' => ($request->input('single') == 'yes') ? true : false,
            'stop_enroll' => ($request->input('stop') == 'yes') ? true : false,
            'manual_auditing' => ($request->input('auditing') == 'yes') ? true : false,
            'enrollDate' => $request->input('enroll'),
            'expireDate' => $request->input('expire'),
            'workTime' => $request->input('work'),
            'restTime' => $request->input('rest'),
            'style' => $request->input('style'),
            'weight' => $max,
        ]);
        $kinds = ClubKind::orderBy('weight')->get();
        return view('app.clubkind', ['kinds' => $kinds])->with('success', '社團類別已經新增完成！');
    }

    public function kindEdit($kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            return view('app.clubeditkind', ['kind' => ClubKind::find($kid)]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function kindUpdate(Request $request, $kid)
    {
        ClubKind::find($kid)->update([
            'name' => $request->input('title'),
            'single' => ($request->input('single') == 'yes') ? true : false,
            'stop_enroll' => ($request->input('stop') == 'yes') ? true : false,
            'manual_auditing' => ($request->input('auditing') == 'yes') ? true : false,
            'enrollDate' => $request->input('enroll'),
            'expireDate' => $request->input('expire'),
            'workTime' => $request->input('work'),
            'restTime' => $request->input('rest'),
            'style' => $request->input('style'),
        ]);
        $kinds = ClubKind::orderBy('weight')->get();
        return view('app.clubkind', ['kinds' => $kinds])->with('success', '社團類別已經修改完成！');
    }

    public function kindRemove(Request $request, $kid)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            ClubKind::destroy($kid);
            $kinds = ClubKind::orderBy('weight')->get();
            return view('app.clubkind', ['kinds' => $kinds])->with('success', '社團類別已經移除！');
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
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
            return $this->kindList();
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
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
            return $this->kindList();
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubList($kid = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            if ($kid) {
                $kind = ClubKind::find($kid);
            } else {
                $kind = ClubKind::first();
            }
            $kinds = ClubKind::orderBy('weight')->get();
            $clubs = Club::orderBy('startDate', 'desc')->get();
            return view('app.clubs', ['kind' => $kind, 'kinds' => $kinds, 'clubs' => $clubs]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
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
            return view('app.clubupload', ['kind' => $kind, 'kinds' => $kinds]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
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
            return $this->clubList($kid)->with('success', '課外社團已經匯入完成！');
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
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
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
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
                $students[] = Student::find($uuid);
            }
            return view('app.clubrepetition', ['kind' => $kid, 'students' => $students]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubCashExport()
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $filename = '學生社團收費統計表';
            $exporter = new ClubCashExport();
            return $exporter->download("$filename.xlsx");
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubAdd($kid = null)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            if ($user->user_type == 'Teacher') {
                $teacher = Teacher::find($user->uuid);
                $unit = $teacher->mainunit->id;    
            } else {
                $unit = 0;
            }
            $kinds = ClubKind::orderBy('weight')->get();
            $units = Unit::main();
            return view('app.clubadd', ['kind' => $kid, 'kinds' => $kinds, 'unit' => $unit, 'units' => $units]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubInsert(Request $request, $kid = null)
    {
        $kind_id =$request->input('kind');
        $title = $request->input('title');
        $found = Club::where('name', $title)->first();
        if ($found) {
            return $this->clubList($kind_id)->with('error', '該課外社團已經存在，無法再新增！');
        }
        $grades = $request->input('grades');
        Club::create([
            'name' => $title,
            'short_name' => $request->input('short'),
            'kind_id' => $kind_id,
            'unit_id' => $request->input('unit'),
            'for_grade' => $grades ?: [],
            'weekdays' => $request->input('weekdays'),
            'self_defined' => $request->has('selfdefine') ? true : false,
            'self_remove' => $request->has('remove') ? true : false,
            'has_lunch' => $request->has('lunch') ? true : false,
            'stop_enroll' => $request->has('stop') ? true : false,
            'startDate' => $request->input('startdate'),
            'endDate' => $request->input('enddate'),
            'startTime' => $request->input('starttime'),
            'endTime' => $request->input('endtime'),
            'teacher' => $request->input('teacher'),
            'location' => $request->input('location'),
            'memo' => $request->input('memo'),
            'cash' => $request->input('cash'),
            'total' => $request->input('total'),
            'maximum' => $request->input('limit'),
        ]);
        return $this->clubList($kind_id)->with('success', '課外社團已經新增完成！');
    }

    public function clubEdit($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $kinds = ClubKind::orderBy('weight')->get();
            $units = Unit::main();
            return view('app.clubedit', ['kinds' => $kinds, 'units' => $units, 'club' => $club]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubUpdate(Request $request, $club_id)
    {
        $club = Club::find($club_id);
        $kind_id =$club->kind_id;
        $grades = $request->input('grades');
        $club->update([
            'name' => $request->input('title'),
            'short_name' => $request->input('short'),
            'kind_id' => $kind_id,
            'unit_id' => $request->input('unit'),
            'for_grade' => $grades ?: [],
            'weekdays' => $request->input('weekdays'),
            'self_defined' => $request->has('selfdefine') ? true : false,
            'self_remove' => $request->has('remove') ? true : false,
            'has_lunch' => $request->has('lunch') ? true : false,
            'stop_enroll' => $request->has('stop') ? true : false,
            'startDate' => $request->input('startdate'),
            'endDate' => $request->input('enddate'),
            'startTime' => $request->input('starttime'),
            'endTime' => $request->input('endtime'),
            'teacher' => $request->input('teacher'),
            'location' => $request->input('location'),
            'memo' => $request->input('memo'),
            'cash' => $request->input('cash'),
            'total' => $request->input('total'),
            'maximum' => $request->input('limit'),
        ]);
        return $this->clubList($kind_id)->with('success', '課外社團已經修改完成！');
    }

    public function clubRemove($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $kind_id = $club->kind_id;
            $club->delete();
            return $this->clubList($kind_id)->with('success', '課外社團已經移除完成！');
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubMail($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            $enrolls = $club->current_enrolls();
            return view('app.clubmail', ['club' => $club, 'enrolls' => $enrolls]);
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubNotify(Request $request, $club_id)
    {
        $club = Club::find($club_id);
        $kind_id = $club->kind_id;
        $enroll_ids = $request->input('enrolls');
        if (!empty($enroll_ids)) {
            $enrolls = ClubEnroll::whereIn('id', $enroll_ids)->get();
            Notification::sendNow($enrolls, new ClubNotification($request->input('message')));
            return $this->clubList($kind_id)->with('success', '已安排於背景進行郵寄作業，郵件將會為您陸續寄出！');    
        }
        return $this->clubList($kind_id)->with('message', '因為沒有寄送對象，已經取消郵寄作業！');
    }

    public function clubPrune($club_id)
    {
        $user = User::find(Auth::user()->id);
        $manager = $user->hasPermission('club.manager');
        if ($user->is_admin || $manager) {
            $club = Club::find($club_id);
            ClubEnroll::where('club_id', $club_id)->where('year', ClubEnroll::current_year())->delete();
            $kind_id = $club->kind_id;
            return $this->clubList($kind_id)->with('success', '已經移除此課外社團的所有報名資訊！');
        } else {
            return view('app.error', ['message' => '您沒有權限使用此功能！']);
        }
    }

    public function clubEnroll()
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') return view('app.error', ['message' => '您不是學生，因此無法報名參加學生社團！']);
        $student = Student::find($user->uuid);
        $grade = substr($student->class_id, 0, 1);
        $clubs = Club::can_enroll($grade);
        return view('app.clubenroll', ['clubs' => $clubs, 'student' => $student]);
    }

    public function clubEnrollAdd($club_id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') return view('app.error', ['message' => '您不是學生，因此無法報名參加學生社團！']);
        $club = Club::find($club_id);
        $student = Student::find($user->uuid);
        return view('app.clubaddenroll', ['club' => $club, 'student' => $student]);
    }

    public function clubEnrollInsert(Request $request, $club_id)
    {
        $user = Auth::user();
        $enroll = ClubEnroll::findBy($user->uuid, $club_id);
        if ($enroll) {
            return $this->clubEnroll()->with('error', '您已經報名該社團，無法再次報名！');
        }
        $club = Club::find($club_id);
        if ($club->kind->single) {
            $student = Student::find($user->uuid);
            $same_kind = $student->current_enrolls_for_kind($club->kind_id);
            if (!empty($same_kind)) return $this->clubEnroll()->with('error', '很抱歉，'.$club->kind->name.'只允許報名參加一個社團！');
        }
        $order = $club->count_enrolls() + 1;
        if ($order > $club->maximum) {
            return $this->clubEnroll()->with('error', '很抱歉，該學生社團已經額滿！');
        }
        $enrolls = Student::find($user->uuid)->year_enrolls();
        $weekdays = null;
        if ($club->self_defined) {
            $weekdays = $request->input('weekdays');
        }
        $conflict = false;
        foreach ($enrolls as $en) {
            $conflict = $en->conflict($club, $weekdays);
            if ($conflict) break;
        }
        if ($conflict) return $this->clubEnroll()->with('error', '很抱歉，此社團與其他已報名的社團上課時段重疊，因此無法報名！');
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
            return $this->clubEnroll()->with('success', '您已經完成報名手續，報名順位為'.$order.'因須進行資格審核，待錄取作業完成後，將另行公告通知！');
        }
        $enroll->accepted = true;
        $enroll->save();
        $message = '';
        if ($order > $club->total) $message = '，目前列為候補，若能遞補錄取將會另行通知！';
        return $this->clubEnroll()->with('success', '您已經完成報名手續，報名順位為'.$order.$message);
    }

    public function clubEnrollEdit($enroll_id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Student') {
            return view('app.error', ['message' => '您不是學生，因此無法修改報名資訊！']);
        }
        $enroll = ClubEnroll::find($enroll_id);
        if ($enroll->uuid != $user->uuid) {
            return view('app.error', ['message' => '這不是您的報名紀錄，因此無法修改！']);
        }
        return view('app.clubeditenroll', ['club' => $enroll->club, 'enroll' => $enroll]);
    }

    public function clubEnrollUpdate(Request $request, $enroll_id)
    {
        $user = Auth::user();
        $enroll = ClubEnroll::find($enroll_id);
        if (!$enroll) {
            return $this->clubEnroll()->with('error', '您要修改的報名紀錄，已經不存在！');
        }
        if ($enroll->uuid != $user->uuid) {
            return view('app.error', ['message' => '這不是您的報名紀錄，因此無法修改！']);
        }
        $enroll->update([
            'need_lunch' => $request->input('lunch') ?: 0,
            'weekdays' => $request->input('weekdays'),
            'identity' => $request->input('identity'),
            'parent' => $request->input('parent'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
        ]);
        return $this->clubEnroll()->with('success', '報名資訊已更新！');
    }

    public function clubEnrollRemove($enroll_id)
    {
        $user = Auth::user();
        $enroll = ClubEnroll::find($enroll_id);
        if ($enroll && $enroll->uuid != $user->uuid) {
            return view('app.error', ['message' => '這不是您的報名紀錄，因此無法修改！']);
        }
        ClubEnroll::destroy($enroll_id);
        return $this->clubEnroll()->with('success', '已為您取消報名！');
    }

}
