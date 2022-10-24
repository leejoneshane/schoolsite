<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Teacher;
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
        if ($user->user_type != 'Teacher') return view('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = Teacher::find($user->uuid);
        $create = ($teacher->role->role_no == 'C02' || $user->is_admin);
        return view('app.meetings', ['date' => $dt->toDateString(), 'create' => $create, 'unit' => $teacher->unit_id, 'meets' => $meets]);
    }

    public function add()
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return view('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = Teacher::find($user->uuid);
        return view('app.meetingadd', ['teacher' => $teacher]);
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return view('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = Teacher::find($user->uuid);
        Meeting::create([
            'unit_id' => $teacher->mainunit->id,
            'role' => $teacher->role->name,
            'reporter' => $teacher->realname,
            'words' => $request->input('words'),
            'inside' => ($request->input('open') == 'yes') ? false : true,
            'expired_at' => ($request->input('enddate')) ?: null,
        ]);
        return $this->index()->with('success', '業務報告已為您張貼！');
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return view('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = Teacher::find($user->uuid);
        $meet = Meeting::find($id);
        if (!$meet) return $this->index()->with('error', '找不到業務報告，因此無法修改內容！');
        return view('app.meetingedit', ['teacher' => $teacher, 'meet' => $meet]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return view('home')->with('error', '只有教職員才能連結此頁面！');
        $teacher = Teacher::find($user->uuid);
        $meet = Meeting::find($id);
        if (!$meet) return $this->index()->with('error', '找不到業務報告，因此無法修改內容！');
        if ($request->input('switch') == 'yes') {
            $meet->update([
                'role' => $teacher->role->name,
                'reporter' => $teacher->realname,
                'words' => $request->input('words'),
                'inside' => ($request->input('open') == 'yes') ? false : true,
                'expired_at' => ($request->input('enddate')) ?: null,
            ]);
        } else {
            $meet->update([
                'words' => $request->input('words'),
                'inside' => ($request->input('open') == 'yes') ? false : true,
                'expired_at' => ($request->input('enddate')) ?: null,
            ]);
        }
        return $this->index()->with('success', '業務報告內容已為您更新！');
    }

    public function remove($id)
    {
        $user = Auth::user();
        if ($user->user_type != 'Teacher') return view('home')->with('error', '只有教職員才能連結此頁面！');
        Meeting::destroy($id);
        return $this->index()->with('success', '業務報告已經移除！');
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
            return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
        }
    }

}
