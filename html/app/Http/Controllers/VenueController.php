<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\User;
use App\Models\Teacher;

class VenueController extends Controller
{

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
        if ($user->is_admin || $manager) {
            $teachers = Teacher::orderBy('realname')->get();
            return view('app.venueadd', ['teacher' => $user->profile, 'teachers' => $teachers]);
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
        if ($user->is_admin || $manager) {
            $venue = Venue::create([
                'name' => $request->input('title'),
                'uuid' => $request->input('manager'),
                'description' => $request->input('description'),
            ]);
            $unavailable = $request->input('unavailable');
            if ($unavailable && $unavailable == 'yes') {
                $venue->unavailable_at = $request->input('startdate');
                $venue->unavailable_until = $request->input('enddate');
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
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        if ($user->is_admin || $manager || $venue->manager->uuid == $user->uuid) {
            $teachers = Teacher::orderBy('realname')->get();
            return view('app.venueedit', ['venue' => $venue, 'teachers' => $teachers]);
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
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        if ($user->is_admin || $manager || $venue->manager->uuid == $user->uuid) {
            $venue = Venue::find($id);
            $venue->update([
                'name' => $request->input('title'),
                'uuid' => $request->input('manager'),
                'description' => $request->input('description'),
            ]);
            $unavailable = $request->input('unavailable');
            if ($unavailable && $unavailable == 'yes') {
                $venue->unavailable_at = $request->input('startdate');
                $venue->unavailable_until = $request->input('enddate');
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
            return redirect()->route('venues')->with('success', '場地/設備更新完成！');
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能修改場地或設備！');
        }
    }

    public function remove($id)
    {
        $user = User::find(Auth::user()->id);
        if ($user->user_type == 'Student') {
            return redirect()->route('home')->with('error', '只有教職員才能預約場地或設備！');
        }
        $manager = ($user->is_admin || $user->hasPermission('venue.manager'));
        if ($user->is_admin || $manager) {
            Venue::destroy($id);
            return redirect()->route('venues')->with('success', '場地/設備已經移除！');
        } else {
            return redirect()->route('venues')->with('error', '只有管理員才能管理場地或設備！');
        }
    }

}
