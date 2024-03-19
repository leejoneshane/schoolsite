<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Watchdog;
use App\Models\User;
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
        $user = User::find(Auth::user()->id);
        $teacher = $user->profile;
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $create = $user->is_admin || $user->hasPermission('meeting.director');
        return view('app.meetings', ['date' => $dt->toDateString(), 'create' => $create, 'unit' => $teacher->mainunit, 'meets' => $meets]);
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        $teacher = $user->profile;
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        if ($user->is_admin || $user->hasPermission('meeting.director')) {
            return view('app.meeting_add', ['teacher' => $teacher]);
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能新增業務報告！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = $user->profile;
        if ($user->is_admin || $user->hasPermission('meeting.director')) {
            $teacher = $user->profile;
            $m = Meeting::create([
                'unit_id' => $teacher->mainunit->id,
                'role' => $teacher->role->name,
                'reporter' => $teacher->realname,
                'words' => $request->input('words'),
                'inside' => !$request->boolean('open'),
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
        $user = User::find(Auth::user()->id);
        $teacher = $user->profile;
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        if ($user->is_admin || $user->hasPermission('meeting.director')) {
            $meet = Meeting::find($id);
            if (!$meet) return redirect()->route('meeting')->with('error', '找不到業務報告，因此無法修改內容！');
            return view('app.meeting_edit', ['teacher' => $teacher, 'meet' => $meet]);
        } else {
            return redirect()->route('meeting')->with('error', '只有主任才能修改業務報告！');
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        if ($user->is_admin || $user->hasPermission('meeting.director')) {
            $meet = Meeting::find($id);
            if (!$meet) return redirect()->route('meeting')->with('error', '找不到業務報告，因此無法修改內容！');
            if ($request->boolean('switch')) {
                $teacher = $user->profile;
                $meet->update([
                    'role' => $teacher->role->name,
                    'reporter' => $teacher->realname,
                    'words' => $request->input('words'),
                    'inside' => !$request->boolean('open'),
                    'expired_at' => ($request->input('enddate')) ?: null,
                ]);
            } else {
                $meet->update([
                    'words' => $request->input('words'),
                    'inside' => !$request->boolean('open'),
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
        $user = User::find(Auth::user()->id);
        if ($user->user_type != 'Teacher') return redirect()->route('home')->with('error', '只有教職員才能連結此頁面！');
        if ($user->is_admin || $user->hasPermission('meeting.director')) {
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
