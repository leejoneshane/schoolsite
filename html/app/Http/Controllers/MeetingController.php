<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Meeting;
use App\Models\Teacher;
use App\Models\Role;

class MeetingController extends Controller
{

    public function index()
    {
        $meets = Meeting::inTime(date('Y-m-d'));
        $user = Auth::user();
        $teacher = Teacher::find($user->uuid);
        $role = Role::find($teacher->role_id);
        $create = ($role->role_no == 'C02' || $user->is_admin);
        return view('app.meetings', ['create' => $create, 'unit' => $teacher->unit_id, 'meets' => $meets]);
    }

}
