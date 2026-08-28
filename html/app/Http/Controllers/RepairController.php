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
use Illuminate\Support\Facades\Notification;
use App\Notifications\RepairNotification;
use App\Notifications\RepairReplyNotification;

class RepairController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        $kinds = RepairKind::all();
        return view('app.repair', ['kinds' => $kinds]);
    }

    public function list($kind = null)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        if ($kind) {
            $kind = RepairKind::find($kind);
        } else {
            $kind = RepairKind::first();
        }
        $jobs = $kind->jobs()->paginate(16);
        return view('app.repair_list', ['kind' => $kind, 'jobs' => $jobs]);
    }

    public function addKind()
    {
        if (!(Auth::user()->is_admin)) {
            return redirect()->route('home')->with('error', '只有管理員才能新增修繕項目！');
        }
        $teachers = Teacher::admins();
        return view('app.repair_addkind', ['teachers' => $teachers]);
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
        return view('app.repair_editkind', ['kind' => $kind, 'teachers' => $teachers]);
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
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        $kind = RepairKind::find($kind);
        return view('app.repair_addjob', ['kind' => $kind]);
    }

    public function insertJob(Request $request, $kind)
    {
        $user = Auth::user();
        $emp = employee();
        $job = RepairJob::create([
            'uuid' => $user->uuid,
            'reporter_name' => $emp ? $emp->realname : '',
            'kind_id' => $kind,
            'place' => $request->input('place'),
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
        ]);
        $mailSent = true;
        try {
            $managers = $job->kind->managers;
            foreach ($managers as $teacher) {
                $manager = $teacher->user;
                if ($manager) {
                    Notification::send($manager, new RepairNotification($job->id));
                }
            }
        } catch (\Throwable $e) {
            $mailSent = false;
        }
        Watchdog::watch($request, '報修登記：' . $job->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        if ($mailSent) {
            return redirect()->route('repair.list', ['kind' => $kind])->with('success', '已完成報修！');
        } else {
            return redirect()->route('repair.list', ['kind' => $kind])->with('message', '報修紀錄已經登錄完成，通知信件暫時無法寄出！請勿重複申報！');
        }
    }

    public function removeJob(Request $request, $job)
    {
        $job = RepairJob::find($job);
        if (!$job) {
            return redirect()->route('repair')->with('error', '找不到要刪除的報修紀錄！');
        }
        $kind = $job->kind_id;
        Watchdog::watch($request, '移除報修紀錄：' . $job->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $job->delete();
        return redirect()->route('repair.list', ['kind' => $kind])->with('success', '報修紀錄已經刪除！');
    }

    public function reply($job)
    {
        $user = Auth::user();
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能登記修繕紀錄！');
        }
        $job = RepairJob::find($job);
        if (!$job) {
            return redirect()->route('repair')->with('error', '找不到該報修紀錄！');
        }
        return view('app.repair_addreply', ['job' => $job]);
    }

    public function insertReply(Request $request, $job)
    {
        $user = Auth::user();
        $emp = employee();
        $reply = RepairReply::create([
            'uuid' => $user->uuid,
            'namager_name' => $emp ? $emp->realname : '',
            'job_id' => $job,
            'status' => $request->input('status'),
            'comment' => $request->input('comment'),
        ]);
        if ($reply->job && $reply->job->reporter && ($reporter = $reply->job->reporter->user)) {
            Notification::send($reporter, new RepairReplyNotification($reply->id));
        }
        Watchdog::watch($request, '修繕回應：' . $reply->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $job_obj = RepairJob::find($job);
        $kind = $job_obj ? $job_obj->kind_id : null;
        return redirect()->route('repair.list', ['kind' => $kind])->with('success', '已回覆修繕結果！');
    }

    public function removeReply(Request $request, $reply)
    {
        $reply = RepairReply::find($reply);
        if (!$reply) {
            return redirect()->route('repair')->with('error', '找不到該修繕回應紀錄！');
        }
        $job = $reply->job ? $reply->job->id : null;
        Watchdog::watch($request, '移除修繕回應：' . $reply->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $reply->delete();
        return redirect()->route('repair.reply', ['job' => $job])->with('success', '修繕回應已經刪除！');
    }

    public function storeImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move(public_path('meeting'), $fileName);
            $url = asset('repair/' . $fileName);
            Watchdog::watch($request, '上傳圖片：' . $url);
            return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
        }
    }

}
