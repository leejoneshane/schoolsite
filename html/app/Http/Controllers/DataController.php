<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Teacher;

class DataController extends Controller
{
    public function all()
    {
        return response()->json(['teachers' => Teacher::all()]);
    }

    public function domain($domain_id)
    {
        $domain = Domain::find($domain_id);
        return response()->json(['teachers' => $domain->teachers()]);
    }

    public function grade($grade_id)
    {
        $grade = Grade::find($grade_id);
        return response()->json(['teachers' => $grade->teachers()]);
    }

    public function class($class_id)
    {
        $myclass = Classroom::find($class_id);
        return response()->json(['teachers' => $myclass->teachers()]);
    }

}
