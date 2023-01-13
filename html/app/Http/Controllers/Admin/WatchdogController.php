<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Watchdog;
use Carbon\Carbon;

class WatchdogController extends Controller
{

    public function index(Request $request)
    {
        $date = $uuid = $ip = $name = $stdid = $stdno = null;
        if ($request->has('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $request->input('date'));
        } else {
            $date = Carbon::today();
        }
        if ($request->has('uuid')) {
            $uuid = $request->input('uuid');
            $logs = Watchdog::where('uuid', $uuid)->latest()->paginate(16);
        } elseif ($request->has('ip')) {
            $ip = $request->input('ip');
            $logs = Watchdog::where('ip', $ip)->latest()->paginate(16);
        } elseif ($request->has('stdid')) {
            $stdid = $request->input('stdid');
            $user = Student::findById($stdid);
            $logs = Watchdog::where('uuid', $user->uuid)->latest()->paginate(16);
        } elseif ($request->has('stdno')) {
            $stdno = $request->input('stdno');
            $user = Student::findByStdno(substr($stdno, 0, 3), substr($stdno, -2));
            $logs = Watchdog::where('uuid', $user->uuid)->latest()->paginate(16);
        } else {
            $logs = Watchdog::whereRaw('DATE(created_at) = ?', $date->format('Y-m-d'))->latest()->paginate(16);
        }
        $teachers = Teacher::orderBy('realname')->get();
        return view('admin.watchdog', ['date' => $date, 'ip' => $ip, 'uuid' => $uuid, 'stdid' => $stdid, 'stdno' => $stdno, 'teachers' => $teachers, 'logs' => $logs]);
    }

}
