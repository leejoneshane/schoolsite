<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\SeniorityImport;
use App\Exports\SeniorityExport;
use App\Models\Seniority;
use App\Models\Teacher;

class OrganizeController extends Controller
{

    public function index($year = null)
    {
        if (Auth::user()->user_type == 'Student') {
            return view('home')->with('error', '您沒有權限使用此功能！');
        }
        $current = current_year();
        if (!$year) $year = $current;
        $years = Seniority::years();
        if (!in_array($current, $years)) $years[] = $current;
        rsort($years);
        $teachers = Seniority::where('syear', $year)->orderBy('no')->paginate(16);
        return view('app.seniority', ['current' => $current, 'year' => $year, 'years' => $years, 'teachers' => $teachers]);
    }

}
