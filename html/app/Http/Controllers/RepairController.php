<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Watchdog;
use App\Models\RepairKind;
use App\Models\RepairJob;
use App\Models\RepairReply;
use Carbon\Carbon;

class RepairController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        $kinds = RepairKind::all();
        return view('app.repair', ['kinds' => $kinds]);
    }

    public function list($kind = null)
    {

    }

    public function addKind()
    {
        if (!(Auth::user()->is_admin)) {
            return redirect()->route('home')->with('error', '只有管理員才能新增修繕項目！');
        }
        $teachers = Teacher::admins();
        return view('app.repairaddkind', ['teachers' => $teachers]);
    }

    public function insertKind(Request $request)
    {
        $kind = RepairKind::create([
            'name' => $request->input('title'),
            'manager' => $request->input('teachers'),
            'description' => $request->input('description'),
        ]);
        Watchdog::watch($request, '新增修繕項目：' . $kind->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('repair')->with('success', '修繕項目新增完成！');
    }

    public function editKind($kind)
    {
        if (!(Auth::user()->is_admin)) {
            return redirect()->route('home')->with('error', '只有管理員才能編輯修繕項目！');
        }
        $kind = RepairKind::find($kind);
        $teachers = Teacher::admins();
        return view('app.repaireditkind', ['kind' => $kind, 'teachers' => $teachers]);
    }

    public function updateKind(Request $request, $kind)
    {
        $kind = RepairKind::find($kind);
        $kind->update([
            'name' => $request->input('title'),
            'manager' => $request->input('teachers'),
            'description' => $request->input('description'),
        ]);
        Watchdog::watch($request, '更新修繕項目：' . $kind->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('repair')->with('success', '修繕項目編輯完成！');
    }

    public function removeKind(Request $request, $kind)
    {
        $kind = RepairKind::find($kind);
        Watchdog::watch($request, '移除修繕項目：' . $kind->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $kind->delete();
        return redirect()->route('repair')->with('success', '修繕項目已經移除！');
    }

    public function report($kind)
    {
    }

    public function insertJob(Request $request, $kind)
    {
    }

}
