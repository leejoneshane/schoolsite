<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use App\Imports\SeniorityImport;
use App\Exports\SeniorityExport;
use App\Models\Seniority;
use App\Models\User;
use App\Models\Unit;
use App\Models\Domain;
use App\Models\Watchdog;

class SeniorityController extends Controller
{

    public function index($search = '')
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = current_year();
        $years = Seniority::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $year = $domain_id = $unit_id = $idno = $realname = $email = '';
        if (!empty($search)) {
            $parameters = explode('&', $search);
            foreach ($parameters as $p) {
                list($key, $val) = explode('=', $p);
                switch ($key) {
                    case 'year':
                        $year = $val;
                        break;
                    case 'unit':
                        $unit_id = $val;
                        break;
                    case 'domain':
                        $domain_id = $val;
                        break;
                    case 'idno':
                        $idno = $val;
                        break;
                    case 'name':
                        $realname = $val;
                        break;
                    case 'email':
                        $email = $val;
                        break;
                }
            }
        }
        if (empty($year)) $year = $current;
        $user = User::find($user->id);
        $manager = $user->is_admin || $user->hasPermission('organize.manager');
        $units = Unit::main();
        $domains = Domain::all();
        $query = Seniority::year_teachers($year)
          ->select('teachers.*', 'belongs.domain_id')
          ->leftJoin('belongs', function (JoinClause $join) use ($year) {
            $join->on('belongs.uuid', '=', 'teachers.uuid')
                 ->where('belongs.year', '=', $year);
        });
        if (!empty($unit_id)) {
            $unit = Unit::find($unit_id);
            $keys = Unit::subkeys($unit->unit_no);
            $query = $query->whereIn('unit_id', $keys);
            $domain_id = $idno = $realname = $email = '';
        }
        if (!empty($domain_id)) {
            $query = $query->where('belongs.domain_id', $domain_id);
            $unit_id = $idno = $realname = $email = '';
        }
        if (!empty($idno)) {
            $query = $query->where('idno', 'like', '%'.$idno.'%');
        }
        if (!empty($realname)) {
            $query = $query->where('realname', 'like', '%'.$realname.'%');
        }
        if (!empty($email)) {
            $query = $query->where('email', 'like', '%'.$email.'%');
        }
        $teachers = $query->orderByRaw('unit_id = 25')->orderBy('tutor_class')->orderBy('belongs.domain_id')->paginate(16)->withQueryString();
        return view('app.seniority', ['current' => $current, 'manager' => $manager, 'year' => $year, 'unit' => $unit_id, 'domain' => $domain_id, 'idno' => $idno, 'realname' => $realname, 'email' => $email, 'years' => $years, 'units' => $units, 'domains' => $domains, 'teachers' => $teachers]);
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
            return view('app.seniority_upload', ['current' => $current]);
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

    public function future()
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '您沒有權限使用此功能！');
        }
        $user = User::find($user->id);
        $manager = $user->hasPermission('organize.manager');
        if ($user->is_admin || $manager) {
            $latest_year = Seniority::latest_year();
            $count = current_year() - $latest_year;
            $teachers = Seniority::year_teachers($latest_year)->get();
            foreach ($teachers as $teacher) {
                $seniority = $teacher->seniority($latest_year);
                $future_year = $seniority->school_year + $count;
                Seniority::updateOrCreate([
                    'uuid' => $teacher->uuid,
                    'syear' => current_year(),
                ],[
                    'school_year' => $future_year,
                    'school_month' => $seniority->school_month,
                    'school_score' => round(($future_year * 12 + $seniority->school_month) / 12 * 0.7, 2),
                    'teach_year' => $seniority->teach_year,
                    'teach_month' => $seniority->teach_month,
                    'teach_score' => round($seniority->teach_score, 2),
                ]);
            }
            $filename = current_year() . '學年度教師教學年資統計（校對稿）';
            return (new SeniorityExport(current_year()))->download("$filename.xlsx");
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
        Watchdog::watch($request, '確認年資無誤：' . $score->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json($score);
    }

    public function cancel(Request $request)
    {
        $uuid = $request->input('uuid');
        $year = $request->input('year') ?: current_year();
        $score = Seniority::findBy($uuid, $year);
        $score->ok = false;
        $score->save();
        Watchdog::watch($request, '懷疑年資有誤：' . $score->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return response()->json($score);
    }

    public function update(Request $request)
    {
        $main = $request->input('main');
        $uuid = $request->input('uuid');
        $year = $request->input('year') ?: current_year();
        $score = Seniority::findBy($uuid, $year);
    if ($main == 'default') {
            $osy = $request->input('school_year');
            $osm = $request->input('school_month');
            $oss = round(($osy * 12 + $osm) / 12 * 0.7, 2); 
            $oty = $request->input('teach_year');
            $otm = $request->input('teach_month');
            $ots = round(($oty * 12 + $otm) / 12 * 0.3, 2);
            $score->school_year = $osy;
            $score->school_month = $osm;
            $score->school_score = $oss;
            $score->teach_year = $oty;
            $score->teach_month = $otm;
            $score->teach_score = $ots;
            $score->save();
            Watchdog::watch($request, '依人事室資料校正年資：' . $score->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));    
        }
        if ($main == 'new') {
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
            Watchdog::watch($request, '修正年資：' . $score->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));                
        }
        return response()->json($score);
    }

}
