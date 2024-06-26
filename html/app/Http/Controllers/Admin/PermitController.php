<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Models\Unit;
use App\Models\Teacher;
use App\Models\Watchdog;

class PermitController extends Controller
{

    public function index()
    {
        $perms = Permission::orderBy('group')->get();
        return view('admin.permission', ['permission' => $perms]);
    }

    public function add()
    {
        return view('admin.permissionadd');
    }

    public function insert(Request $request)
    {
        $app = $request->input('app');
        $perm = $request->input('perm');
        $desc = $request->input('desc');
        $ckf = Permission::findByName("$app.$perm");
        if ($ckf) {
            return back()->withInput()->with('error', '該權限已經存在，無法再新增！');
        }
        $p = Permission::create([
            'group' => $app,
            'permission' => $perm,
            'description' => $desc,
        ]);
        Watchdog::watch($request, '新增權限：' . $p->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('permission')->with('success', '權限新增完成！');
    }

    public function edit($id)
    {
        $perm = Permission::find($id);
        return view('admin.permissionedit', ['perm' => $perm]);
    }

    public function update(Request $request, $id)
    {
        $app = $request->input('app');
        $perm = $request->input('perm');
        $desc = $request->input('desc');
        $ckf = Permission::findByName("$app.$perm");
        if ($ckf && $ckf->id != $id) {
            return back()->withInput()->with('error', '該權限已經存在，無法修改成新的代號！');
        }
        $p = Permission::find($id);
        $p->update([
            'group' => $app,
            'permission' => $perm,
            'description' => $desc,
        ]);
        Watchdog::watch($request, '更新權限：' . $p->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect()->route('permission')->with('success', '權限更新完成！');
    }

    public function remove(Request $request, $id)
    {
        $p = Permission::find($id);
        Watchdog::watch($request, '移除權限：' . $p->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $p->delete();
        return redirect()->route('permission')->with('success', '權限已經移除！');
    }

    public function grantList($id)
    {
        $perm = Permission::find($id);
        $units = Unit::main();
        $already = $perm->teachers()->orderBy('uuid')->get();
        $teachers = Teacher::leftJoin('units', 'units.id', '=', 'unit_id')
            ->leftJoin('roles', 'roles.id', '=', 'role_id')
            ->orderBy('units.unit_no')
            ->orderBy('roles.role_no')
            ->get()
            ->reject(function ($teacher) {
                return $teacher->user->is_admin;
            });
        return view('admin.grant', ['permission' => $perm, 'already' => $already, 'units' => $units, 'teachers' => $teachers]);
    }

    public function grantUpdate(Request $request, $id)
    {
        $users = $request->input('teachers');
        $perm = Permission::find($id)->removeAll();
        if (!empty($users)) {
            $user_list = [];
            $perm->assign($users);
            foreach ($users as $u) {
                $user_list[] = Teacher::find($u)->realname;
            }
            $log = '授予權限' . $perm->description . '給' . implode('、', $user_list);
            Watchdog::watch($request, $log);
        } else {
            $log = '已經移除所有授權！';
            Watchdog::watch($request, '移除' . $perm->description . '所有已授權人員！');
        }
        return back()->with('success', $log);
    }

    public function admin()
    {
        $units = Unit::main();
        $uuids = User::admins()->map(function ($user) {
            return $user->uuid;
        })->toArray();
        $already = Teacher::whereIn('uuid', $uuids)->get();
        $teachers = Teacher::leftJoin('units', 'units.id', '=', 'unit_id')
            ->leftJoin('roles', 'roles.id', '=', 'role_id')
            ->orderBy('units.unit_no')
            ->orderBy('roles.role_no')
            ->get();
        return view('admin.grant_admin', ['already' => $already, 'units' => $units, 'teachers' => $teachers]);
    }

    public function adminUpdate(Request $request)
    {
        $users = $request->input('teachers');
        $others = User::admins()->whereNotIn('uuid', $users);
        if (!empty($others)) {
            foreach ($others as $o) {
                $o->is_admin = false;
                $o->save();
            }
        }
        if (!empty($users)) {
            $user_list = [];
            foreach ($users as $u) {
                $user_list[] = Teacher::find($u)->realname;
                $temp = User::where('uuid', $u)->first();
                $temp->is_admin = true;
                $temp->save();
            }
            $log = '授予系統管理員權限給' . implode('、', $user_list);
            Watchdog::watch($request, $log);
        } else {
            $log = '已經移除所有授權！';
            Watchdog::watch($request, '移除系統管理員所有已授權人員！');
        }
        return back()->with('success', $log);
    }

}
