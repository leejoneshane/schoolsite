<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OrganizeSettings;
use App\Models\OrganizeSurvey;
use App\Models\OrganizeVacancy;
use App\Models\Seniority;
use App\Models\Teacher;

class OrganizeController extends Controller
{

    public function index($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $teacher = Teacher::find($user->uuid);
        $reserve = DB::table('organize_reserved')->where('syear', current_year())->where('uuid', $user->uuid)->first();
        $reserved = ($reserve) ? true : false;
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $flow = OrganizeSettings::where('syear', $year)->first();
        $stage1 = OrganizeVacancy::current_stage(1);
        $stage2 = OrganizeVacancy::current_stage(1);
        return view('app.organize', ['current' => $current, 'year' => $year, 'years' => $years, 'flow' => $flow, 'reserved' => $reserved, 'teacher' => $teacher, 'stage1' => $stage1, 'stage2' => $stage2]);
    }

    public function list($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $teacher = Teacher::find($user->uuid);
        $reserve = DB::table('organize_reserved')->where('syear', current_year())->where('uuid', $user->uuid)->first();
        $reserved = ($reserve) ? true : false;
        $current = current_year();
        if (!$year) $year = $current;
        $years = OrganizeSettings::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $flow = OrganizeSettings::where('syear', $year)->first();
        $stage1 = OrganizeVacancy::current_stage(1);
        $stage2 = OrganizeVacancy::current_stage(1);
        return view('app.organize', ['current' => $current, 'year' => $year, 'years' => $years, 'flow' => $flow, 'reserved' => $reserved, 'teacher' => $teacher, 'stage1' => $stage1, 'stage2' => $stage2]);
    }

}
