<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Unit;
use App\Models\Teacher;

class PermitController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
        Permission::create([
            'group' => $app,
            'permission' => $perm,
            'description' => $desc,
        ]);
        return redirect()->route('permission')->index(['success' => '權限新增完成！']);
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
        Permission::find($id)->update([
            'group' => $app,
            'permission' => $perm,
            'description' => $desc,
        ]);
        return redirect()->route('permission')->with('success', '權限更新完成！');
    }

    public function remove($id)
    {
        Permission::destroy($id);
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
        $log = '已經移除所有授權！';
        if (!empty($users)) {
            $perm->assignByUUID($users);
            $log .= '並重新授權給指定人員！';
        }
        return back()->with('success', $log);
    }

}
