<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\VenueReserve;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Watchdog;
use Carbon\Carbon;

class VenueController extends Controller
{
    protected static $sessionMap = [
        0 => '早自習',
        1 => '第一節',
        2 => '第二節',
        3 => '第三節',
        4 => '第四節',
        5 => '午休',
        6 => '第五節',
        7 => '第六節',
        8 => '第七節',
        9 => '課後',
    ];

    public function index()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        $venues = Venue::all();
        return view('app.venues', ['manager' => $manager, 'venues' => $venues]);
    }

    public function add()
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        if ($manager) {
            $teachers = Teacher::admins();
            return view('app.venue_add', ['teacher' => $user->profile, 'teachers' => $teachers, 'sessions' => self::$sessionMap]);
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能新增場地或設備！');
        }
    }

    public function insert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        if ($manager) {
            $venue = Venue::create([
                'name' => $request->input('title'),
                'uuid' => $request->input('manager'),
                'description' => $request->input('description'),
            ]);
            if ($request->hasFile('reserved_info')) {
                $extension = $request->file('reserved_info')->getClientOriginalExtension();
                $fileName = $venue->id . '.' . $extension;
                $request->file('reserved_info')->move(public_path('venue'), $fileName);
                $url = asset('venue/' . $fileName);
                Watchdog::watch($request, '上傳教案：' . $url);
                $venue->reserved_info = $fileName;
            }
            $unavailable = $request->input('unavailable');
            if ($unavailable && $unavailable == 'yes') {
                $venue->unavailable_at = $request->input('startdate');
                $venue->unavailable_until = $request->input('enddate');
            }
            $map = $request->input('map');
            $schedule = collect();
            for ($i=0; $i<5; $i++) {
                for ($j=0; $j<10; $j++) {
                    if (isset($map[$i][$j]) && $map[$i][$j] == 'yes') {
                        $schedule->push([ 'weekday' => $i, 'session' => $j ]);
                    }
                }
            }
            $venue->availability = $schedule;
            $start = $request->integer('start');
            if ($start) {
                $venue->schedule_start = $start;
            } else {
                $venue->schedule_start = 0;
            }
            $limit = $request->integer('limit');
            if ($limit) {
                $venue->schedule_limit = $limit;
            } else {
                $venue->schedule_limit = 0;
            }
            if ($request->has('open') && $request->input('open') == 'yes') {
                $venue->open = true;
            }
            $venue->save();
            Watchdog::watch($request, '新增可預約場地或設備：' . $venue->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('venues')->with('success', '場地/設備新增完成！');
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能新增場地或設備！');
        }
    }

    public function edit($id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $venue = Venue::find($id);
        if (!$venue) return redirect()->route('venues')->with('error', '找不到此場地/設備，因此無法編輯！');
        $manager = ($user->is_admin || $user->hasPermission('venue.manager') || $venue->manager->uuid == $user->uuid);
        if ($manager) {
            $teachers = Teacher::admins();
            return view('app.venue_edit', ['venue' => $venue, 'teachers' => $teachers]);
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能修改場地或設備！');
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $venue = Venue::find($id);
        if (!$venue) return redirect()->route('venues')->with('error', '找不到此場地/設備，因此無法編輯！');
        $manager = ($user->is_admin || $user->hasPermission('venue.manager') || $venue->manager->uuid == $user->uuid);
        if ($manager) {
            $venue = Venue::find($id);
            $venue->update([
                'name' => $request->input('title'),
                'uuid' => $request->input('manager'),
                'description' => $request->input('description'),
            ]);
            if ($request->hasFile('reserved_info')) {
                if ($venue->reserved_info) {
                    $path = public_path('venue/' . $venue->reserved_info);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $extension = $request->file('reserved_info')->getClientOriginalExtension();
                $fileName = $venue->id . '.' . $extension;
                $request->file('reserved_info')->move(public_path('venue'), $fileName);
                $url = asset('venue/' . $fileName);
                Watchdog::watch($request, '上傳教案：' . $url);
                $venue->reserved_info = $fileName;
            }
            $unavailable = $request->input('unavailable');
            if ($unavailable && $unavailable == 'yes') {
                $venue->unavailable_at = $request->input('startdate');
                $venue->unavailable_until = $request->input('enddate');
            } else {
                $venue->unavailable_at = null;
                $venue->unavailable_until = null;
            }
            $map = $request->input('map');
            $schedule = collect();
            for ($i=0; $i<5; $i++) {
                for ($j=0; $j<10; $j++) {
                    if (isset($map[$i][$j]) && $map[$i][$j] == 'yes') {
                        $schedule->push([ 'weekday' => $i, 'session' => $j ]);
                    }
                }
            }
            $venue->availability = $schedule;
            $start = $request->integer('start');
            if ($start) {
                $venue->schedule_start = $start;
            } else {
                $venue->schedule_start = 0;
            }
            $limit = $request->integer('limit');
            if ($limit) {
                $venue->schedule_limit = $limit;
            } else {
                $venue->schedule_limit = 0;
            }
            if ($request->has('open') && $request->input('open') == 'yes') {
                $venue->open = true;
            }
            $venue->save();
            Watchdog::watch($request, '更新可預約場地或設備：' . $venue->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return redirect()->route('venues')->with('success', '場地/設備更新完成！');
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能修改場地或設備！');
        }
    }

    public function remove(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        if ($manager) {
            $venue = Venue::find($id);
            Watchdog::watch($request, '移除可預約場地或設備：' . $venue->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $venue->delete();
            return redirect()->route('venues')->with('success', '場地/設備已經移除！');
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能管理場地或設備！');
        }
    }

    public function reserve($id, $date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } else {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $venue = Venue::find($id);
        if (!$venue) return redirect()->route('venues')->with('error', '找不到此場地/設備，因此無法預約！');
        $result = $venue->weekly($date);
        return view('app.venue_reserve', ['date' => $date, 'venue' => $venue, 'result' => $result]);
    }

    public function show(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $reserve = VenueReserve::find($request->input('id'));
        $header = '場地/設備預約紀錄';
        if (!$reserve) {
            $body = '找不到預約記錄！';
        } else {
            $body = view('app.venue_log', ['reserve' => $reserve])->render();
        }
        return response()->json((object) [ 'header' => $header, 'body' => $body]);
    }

    public function reserveAdd(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $venue_id = $request->input('id');
        $venue = Venue::find($venue_id);
        $reserve_date = substr($request->input('date'), 0, 10);
        $weekday = intval($request->input('weekday'));
        $session = intval($request->input('session'));
        $sessionStr = self::$sessionMap[$session];
        $max = intval($request->input('max'));
        return view('app.venue_booking', ['date' => $reserve_date, 'venue' => $venue, 'weekday' => $weekday, 'session' => $session, 'session_name' => $sessionStr ,'max' => $max]);
    }

    public function reserveInsert(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $venue_id = $request->input('venue_id');
        $date = $request->input('date');
        $r = VenueReserve::create([
            'venue_id' => $venue_id,
            'uuid' => $user->uuid,
            'teacher_name' => $user->profile->realname,
            'reserved_at' => $date,
            'weekday' => $request->input('weekday'),
            'session' => $request->input('session'),
            'length' => $request->input('length'),
            'reason' => $request->input('reason'),
        ]);
        Watchdog::watch($request, '新增場地或設備預約紀錄：' . $r->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('venue.reserve', ['id' => $venue_id, 'date' => $date])->with('success', '已經為您預約場地或設備！');
    }

    public function reserveEdit(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $reserve = VenueReserve::find($request->input('id'));
        if (!$reserve) return redirect()->route('venues')->with('error', '找不到此預約紀錄，因此無法編輯！');
        $manager = ($user->is_admin || $user->hasPermission('venue.manager') || $user->uuid == $reserve->subscriber->uuid);
        if ($manager) {
            $session_name = self::$sessionMap[$reserve->session];
            $result = $reserve->venue->weekly(substr($reserve->reserved_at, 0, 10));
            $max = 1;
            for ($j=$reserve->session + 1; $j<10; $j++) {
                if ($result->map[$reserve->weekday][$j] === true) {
                    $max++;
                } else {
                    break;
                }
            }
            return view('app.venue_editbooking', ['reserve' => $reserve, 'session_name' => $session_name, 'max' => $max]);
        } else {
            return redirect()->route('venues')->with('error', '只有預約者才能管理場地或設備！');
        }
    }

    public function reserveUpdate(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $reserve = VenueReserve::find($request->input('id'));
        if (!$reserve) return redirect()->route('venues')->with('error', '找不到此預約紀錄，因此無法編輯！');
        $manager = ($user->is_admin || $user->hasPermission('venue.manager') || $user->uuid == $reserve->subscriber->uuid);
        if ($manager) {
            if ($request->input('act') == 'edit') {
                $reserve->update([
                    'length' => $request->input('length'),
                    'reason' => $request->input('reason'),
                ]);
                Watchdog::watch($request, '修改場地或設備預約紀錄：' . $reserve->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                return redirect()->route('venue.reserve', ['id' => $reserve->venue->id, 'date' => substr($reserve->reserved_at, 0, 10)])->with('success', '已經為您修改預約紀錄！');
            } else {
                Watchdog::watch($request, '移除場地或設備預約紀錄：' . $reserve->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $reserve->delete();
                return redirect()->route('venue.reserve', ['id' => $reserve->venue->id, 'date' => substr($reserve->reserved_at, 0, 10)])->with('success', '已經為您取消預約！');
            }
        } else {
            return redirect()->route('venues')->with('error', '只有預約者才能管理場地或設備！');
        }
    }

}
