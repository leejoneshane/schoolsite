<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Watchdog;
use Carbon\Carbon;

class MeetingController extends Controller
{

    public function index($date = null)
    {
        if ($date) {
            $dt = Carbon::createFromFormat('Y-m-d', $date);
        } else {
            $dt = Carbon::today();
        }
        $meets = Meeting::inTime($dt);
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        $create = ($teacher->role->role_no == 'C02' || $user->is_admin);
        return view('app.meetings', ['date' => $dt->toDateString(), 'create' => $create, 'unit' => $teacher->unit_id, 'meets' => $meets]);
    }

    public function add()
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        if ($teacher->role->role_no == 'C02' || $user->is_admin) {
            return view('app.meetingadd', ['teacher' => $teacher]);
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能新增業務報告！');
        }
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        if ($teacher->role->role_no == 'C02' || $user->is_admin) {
            $teacher = $user->profile;
            $m = Meeting::create([
                'unit_id' => $teacher->mainunit->id,
                'role' => $teacher->role->name,
                'reporter' => $teacher->realname,
                'words' => $request->input('words'),
                'inside' => $request->boolean('open'),
                'expired_at' => ($request->input('enddate')) ?: null,
            ]);
            Watchdog::watch($request, '新增網路朝會業務報告：' . $m->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('meeting')->with('success', '業務報告已為您張貼！');
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能新增業務報告！');
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        if ($teacher->role->role_no == 'C02' || $user->is_admin) {
            $meet = Meeting::find($id);
            if (!$meet) return redirect()->route('meeting')->with('error', '找不到業務報告，因此無法修改內容！');
            return view('app.meetingedit', ['teacher' => $teacher, 'meet' => $meet]);
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能修改業務報告！');
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        if ($teacher->role->role_no == 'C02' || $user->is_admin) {
            $meet = Meeting::find($id);
            if (!$meet) return redirect()->route('meeting')->with('error', '找不到業務報告，因此無法修改內容！');
            if ($request->boolean('switch')) {
                $meet->update([
                    'role' => $teacher->role->name,
                    'reporter' => $teacher->realname,
                    'words' => $request->input('words'),
                    'inside' => $request->boolean('open'),
                    'expired_at' => ($request->input('enddate')) ?: null,
                ]);
            } else {
                $meet->update([
                    'words' => $request->input('words'),
                    'inside' => $request->boolean('open'),
                    'expired_at' => ($request->input('enddate')) ?: null,
                ]);
            }
            Watchdog::watch($request, '更新網路朝會業務報告：' . $meet->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('meeting')->with('success', '業務報告內容已為您更新！');
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能修改業務報告！');
        }
    }

    public function remove(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        if ($teacher->role->role_no == 'C02' || $user->is_admin) {
            $m = Meeting::find($id);
            Watchdog::watch($request, '移除網路朝會業務報告：' . $m->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $m->delete();
            return redirect()->route('meeting')->with('success', '業務報告已經移除！');
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能移除業務報告！');
        }
    }

    public function storeImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move(public_path('meeting'), $fileName);
            $url = asset('meeting/' . $fileName);
            Watchdog::watch($request, '上傳圖片：' . $url);
            return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
        }
    }

}
