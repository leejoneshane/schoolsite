<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Seniority;

class SeniorityController extends Controller
{

    public function index($year = null)
    {
        $current = Seniority::current_year();
        if (!$year) $year = $current;
        $years = Seniority::years();
        $seniority = Seniority::where('year', $year)->get();
        return view('app.seniority', ['current' => $current, 'year' => $year, 'years' => $years, 'teachers' => $seniority]);
    }

}
