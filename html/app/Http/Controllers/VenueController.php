<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\User;

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
            return view('app.venueadd', ['teacher' => $user->profile]);
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
            Venue::create([
                'name' => $request->input('name'),
                'manager' => $request->input('uuid'),
                'description' => $request->input('description'),
            ]);
            return redirect()->route('venues')->with('success', '場地/設備新增完整！');
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
            return view('app.venueedit', ['venue' => $venue]);
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
            Venue::find($id)->update([
                'name' => $request->input('name'),
                'manager' => $request->input('uuid'),
                'description' => $request->input('description'),
            ]);
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
