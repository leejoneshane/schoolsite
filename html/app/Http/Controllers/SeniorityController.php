<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\SeniorityImport;
use App\Exports\SeniorityExport;
use App\Models\Seniority;
use App\Models\User;
use App\Models\Watchdog;

class SeniorityController extends Controller
{

    public function index($year = null)
    {
        if (Auth::user()->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = current_year();
        if (!$year) $year = $current;
        $years = Seniority::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $teachers = Seniority::where('syear', $year)->orderBy('no')->paginate(16);
        return view('app.seniority', ['current' => $current, 'year' => $year, 'years' => $years, 'teachers' => $teachers]);
    }

    public function upload($kid = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $user = User::find($user->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            $current = current_year();
            return view('app.seniorityupload', ['current' => $current]);
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $user = User::find($user->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            $importer = new SeniorityImport();
            $importer->import($request->file('excel'));
            Watchdog::watch($request, '匯入年資統計：' . $request->file('excel')->path());
            return redirect()->route('seniority')->with('success', '教職員年資已經匯入完成！');
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function export($year = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $user = User::find($user->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            $current = current_year();
            if (!$year) $year = $current;
            $filename = $year . '學年度教師教學年資統計';
            return (new SeniorityExport($year))->download("$filename.xlsx");
        } else {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
    }

    public function confirm(Request $request)
    {
        $uuid = $request->input('uuid');
        $year = $request->input('year') ?: current_year();
        $score = Seniority::findBy($uuid, $year);
        $score->ok = true;
        $score->save();
        Watchdog::watch($request, '確認年資無誤：' . $score->toJson());
        return response()->json($score);
    }

    public function cancel(Request $request)
    {
        $uuid = $request->input('uuid');
        $year = $request->input('year') ?: current_year();
        $score = Seniority::findBy($uuid, $year);
        $score->ok = false;
        $score->save();
        Watchdog::watch($request, '懷疑年資有誤：' . $score->toJson());
        return response()->json($score);
    }

    public function update(Request $request)
    {
        $uuid = $request->input('uuid');
        $year = $request->input('year') ?: current_year();
        $score = Seniority::findBy($uuid, $year);
        $nsy = $request->input('new_school_year');
        $nsm = $request->input('new_school_month');
        $nss = round(($nsy * 12 + $nsm) / 12 * 0.7, 2); 
        $nty = $request->input('new_teach_year');
        $ntm = $request->input('new_teach_month');
        $nts = round(($nty * 12 + $ntm) / 12 * 0.3, 2); 
        $score->new_school_year = $nsy;
        $score->new_school_month = $nsm;
        $score->new_school_score = $nss;
        $score->new_teach_year = $nty;
        $score->new_teach_month = $ntm;
        $score->new_teach_score = $nts;
        $score->save();
        Watchdog::watch($request, '修正年資：' . $score->toJson());
        return response()->json($score);
    }

}
