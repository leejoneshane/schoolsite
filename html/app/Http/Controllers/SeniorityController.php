<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\SeniorityImport;
use App\Exports\SeniorityExport;
use App\Models\Seniority;
use App\Models\Teacher;

class SeniorityController extends Controller
{

    public function index($year = null)
    {
        if (Auth::user()->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = Seniority::current_year();
        if (!$year) $year = $current;
        $years = Seniority::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $teachers = Seniority::where('syear', $year)->orderBy('no')->paginate(16);
        return view('app.seniority', ['current' => $current, 'year' => $year, 'years' => $years, 'teachers' => $teachers]);
    }

    public function upload($kid = null)
    {
        if (Auth::user()->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = Seniority::current_year();
        return view('app.seniorityupload', ['current' => $current]);
    }

    public function import(Request $request)
    {
        if (Auth::user()->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $importer = new SeniorityImport();
        $importer->import($request->file('excel'));
        return $this->index()->with('success', '教職員年資已經匯入完成！');
    }

    public function export()
    {
        if (Auth::user()->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $filename = Seniority::current_year() . '學年度教師教學年資統計';
        return (new SeniorityExport())->download("$filename.xlsx");
    }

    public function edit()
    {
        if (Auth::user()->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
    }

}
