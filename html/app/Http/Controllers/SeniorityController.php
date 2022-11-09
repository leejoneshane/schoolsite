<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\SeniorityImport;
use App\Models\Seniority;

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
        $seniority = Seniority::where('syear', $year)->get();
        return view('app.seniority', ['current' => $current, 'year' => $year, 'years' => $years, 'teachers' => $seniority]);
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

    }
}
