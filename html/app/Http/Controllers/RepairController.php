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
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        if ($kind) {
            $kind = RepairKind::find($kind);
        } else {
            $kind = RepairKind::first();
        }
        $jobs = $kind->jobs()->paginate(16);
        return view('app.repairlist', ['kind' => $kind, 'jobs' => $jobs]);
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
            'selftest' => $request->input('selftest'),
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
            'selftest' => $request->input('selftest'),
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
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        $kind = RepairKind::find($kind);
        return view('app.repairaddjob', ['kind' => $kind]);
    }

    public function insertJob(Request $request, $kind)
    {
        $job = RepairJob::create([
            'uuid' => $request->user()->uuid,
            'kind_id' => $kind,
            'place' => $request->input('place'),
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
        ]);
        Watchdog::watch($request, '報修登記：' . $job->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('repair.list', ['kind' => $kind])->with('success', '已完成報修！');
    }

    public function removeJob(Request $request, $job)
    {
        $job = RepairJob::find($job);
        $kind = $job->kind_id;
        Watchdog::watch($request, '移除報修紀錄：' . $job->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $job->delete();
        return redirect()->route('repair.list', ['kind' => $kind])->with('success', '報修紀錄已經刪除！');
    }

    public function reply($job)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        $job = RepairJob::find($job);
        return view('app.repairaddreply', ['job' => $job]);
    }

    public function insertReply(Request $request, $job)
    {
        $reply = RepairReply::create([
            'uuid' => $request->user()->uuid,
            'job_id' => $job,
            'status' => $request->input('status'),
            'comment' => $request->input('comment'),
        ]);
        Watchdog::watch($request, '修繕回應：' . $reply->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $kind = RepairJob::find($job)->kind_id;
        return redirect()->route('repair.list', ['kind' => $kind])->with('success', '已回覆修繕結果！');
    }

    public function removeReply(Request $request, $reply)
    {
        $reply = RepairReply::find($reply);
        $job = $reply->job->id;
        Watchdog::watch($request, '移除修繕回應：' . $reply->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $reply->delete();
        return redirect()->route('repair.reply', ['job' => $job])->with('success', '修繕回應已經刪除！');
    }

}
