<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\ClubKind;
use App\Models\ClubEnroll;
use App\Models\Student;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ClubController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $manager = $user->hasPermission('club.manager');
        return view('app.club', ['manager' => ($user->is_admin || $manager)]);
    }

    public function kindList()
    {
        $user = Auth::user();
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
        $user = Auth::user();
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
        $user = Auth::user();
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
        $user = Auth::user();
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
        $user = Auth::user();
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
        $user = Auth::user();
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
        $user = Auth::user();
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

}
