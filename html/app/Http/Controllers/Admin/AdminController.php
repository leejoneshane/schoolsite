<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Permission;
use App\Jobs\SyncFromTpedu;
use App\Jobs\SyncToAd;
use App\Jobs\SyncToGsuite;
use App\Providers\TpeduServiceProvider as SSO;
use App\Providers\ADServiceProvider as AD;
use App\Providers\GsuiteServiceProvider as GSUITE;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!Menu::find('database')) {
            Menu::create([
                'id' => 'database',
                'parent_id' => 'admin',
                'caption' => '校務資料管理',
                'url' => '#',
                'weight' => 10,
            ]);
            if (!Menu::find('sync')) {
                Menu::create([
                    'id' => 'sync',
                    'parent_id' => 'database',
                    'caption' => '更新快取資料',
                    'url' => 'route.sync',
                    'weight' => 10,
                ]);
            }
            if (!Menu::find('ADsync')) {
                Menu::create([
                    'id' => 'ADsync',
                    'parent_id' => 'database',
                    'caption' => '同步到 AD',
                    'url' => 'route.syncAD',
                    'weight' => 30,
                ]);
            }
            if (!Menu::find('Gsuitesync')) {
                Menu::create([
                    'id' => 'Gsuitesync',
                    'parent_id' => 'database',
                    'caption' => '同步到 Google',
                    'url' => 'route.syncGsuite',
                    'weight' => 40,
                ]);
            }
            if (!Menu::find('units')) {
                Menu::create([
                    'id' => 'units',
                    'parent_id' => 'database',
                    'caption' => '行政單位與職稱',
                    'url' => 'route.units',
                    'weight' => 50,
                ]);
            }
            if (!Menu::find('classes')) {
                Menu::create([
                    'id' => 'classes',
                    'parent_id' => 'database',
                    'caption' => '年級與班級',
                    'url' => 'route.classes',
                    'weight' => 60,
                ]);
            }
            if (!Menu::find('subjects')) {
                Menu::create([
                    'id' => 'subjects',
                    'parent_id' => 'database',
                    'caption' => '學習科目',
                    'url' => 'route.subjects',
                    'weight' => 70,
                ]);
            }
            if (!Menu::find('teachers')) {
                Menu::create([
                    'id' => 'teachers',
                    'parent_id' => 'database',
                    'caption' => '教職員',
                    'url' => 'route.teachers',
                    'weight' => 80,
                ]);
            }
            if (!Menu::find('students')) {
                Menu::create([
                    'id' => 'students',
                    'parent_id' => 'database',
                    'caption' => '學生',
                    'url' => 'route.students',
                    'weight' => 90,
                ]);
            }
        }
        if (!Menu::find('website')) {
            Menu::create([
                'id' => 'website',
                'parent_id' => 'admin',
                'caption' => '網站組態管理',
                'url' => '#',
                'weight' => 20,
            ]);
            if (!Menu::find('menus')) {
                Menu::create([
                    'id' => 'menus',
                    'parent_id' => 'website',
                    'caption' => '選單管理',
                    'url' => 'route.menus',
                    'weight' => 10,
                ]);
            }
            if (!Menu::find('permission')) {
                Menu::create([
                    'id' => 'permission',
                    'parent_id' => 'website',
                    'caption' => '權限管理',
                    'url' => 'route.permission',
                    'weight' => 20,
                ]);
            }
        }
    }

    public function index()
    {
        return view('admin');
    }

    public function syncFromTpedu()
    {
        return view('admin.tpedu');
    }

    public function startSyncFromTpedu(Request $request)
    {
        $expire = ($request->input('expire') == 'yes') ? true : false;
        $password = ($request->input('password') == 'sync') ? true : false;
        $unit = ($request->input('sync_units') == 'yes') ? true : false;
        $classroom = ($request->input('sync_classes') == 'sync') ? true : false;
        $subject = ($request->input('sync_subjects') == 'sync') ? true : false;
        $remove = ($request->input('leave') == 'remove') ? true : false;
        SyncFromTpedu::dispatch($expire, $password, $unit, $classroom, $subject, $remove);
        session()->flash('success', '同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
        return view('admin');
    }

    public function syncToAD()
    {
        return view('admin.ad');
    }

    public function startSyncToAD(Request $request)
    {
        $password = ($request->input('password') == 'sync') ? true : false;
        $leave = $request->input('leave');
        SyncToAd::dispatch($password, $leave);
        session()->flash('success', 'AD 同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
        return view('admin');
    }

    public function syncToGsuite()
    {
        $classes = Classroom::all();
        return view('admin.gsuite', ['classes' => $classes]);
    }

    public function startSyncToGsuite(Request $request)
    {
        $password = ($request->input('password') == 'sync') ? true : false;
        $leave = $request->input('leave');
        $target = false;
        if ($leave == 'onduty') $target = $request->input('target');
        SyncToGsuite::dispatch($password, $leave, $target);
        session()->flash('success', 'Google 同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
        return view('admin');
    }

    public function unitList()
    {
        $units = Unit::with('roles')->orderBy('unit_no')->get();
        return view('admin.units', ['units' => $units]);
    }

    public function unitUpdate(Request $request)
    {
        if ($request->has('units')) {
            $units = $request->input('units');
            $unit_ids = $request->input('uid');
            foreach ($units as $id => $name) {
                $unit = Unit::find($id);
                $unit->unit_no = $unit_ids[$id];
                $unit->name = $name;
                $unit->save();
            }
            session()->flash('success', '行政單位已更新並儲存！');
        }
        if ($request->has('roles')) {
            $roles = $request->input('roles');
            $role_ids = $request->input('rid');
            foreach ($roles as $id => $name) {
                $role = Role::find($id);
                $role->role_no = $role_ids[$id];
                $role->name = $name;
                $role->save();    
            }
            session()->flash('success', '職稱已更新並儲存！');
        }
        return $this->unitList();
    }

    public function unitAdd()
    {
        return view('admin.unitadd');
    }

    public function unitInsert(Request $request)
    {
        $input = $request->only(['unit_id', 'unit_name']);
        if ($input) {
            Unit::create([
                'unit_no' => $input['unit_id'],
                'name' => $input['unit_name'],
            ]);
        }
        session()->flash('success', '行政單位已新增完成！');
        return $this->unitList();
    }

    public function roleAdd()
    {
        $units = Unit::with('roles')->orderBy('unit_no')->get();
        return view('admin.roleadd', ['units' => $units]);
    }

    public function roleInsert(Request $request)
    {
        $input = $request->only(['role_id', 'role_unit', 'role_name']);
        if ($input) {
            Role::create([
                'role_no' => $input['role_id'],
                'unit_id' => $input['role_unit'],
                'name' => $input['role_name'],
            ]);
        }
        session()->flash('success', '職務層級已新增完成！');
        return $this->unitList();
    }

    public function classList()
    {
        $grades = Grade::all();
        $classes = Classroom::all();
        $teachers = Teacher::orderBy('realname')->get();
        return view('admin.classes', ['grades' => $grades, 'classes' => $classes, 'teachers' => $teachers]);
    }

    public function classUpdate(Request $request)
    {
        $names = $request->input('name');
        $tutors = $request->input('tutor');
        foreach ($names as $id => $name) {
            $cls = Classroom::find($id);
            $old_tutors = $cls->tutors;
            $found = false;
            foreach ($old_tutors as $t) {
                if ($t->uuid == $tutors[$id]) {
                    $found = true;
                    continue;
                } else {
                    $t->tutor_class = null;
                    $t->save();
                }
            }
            if (!$found) {
                $new_tutor = Teacher::find($tutors[$id]);
                $new_tutor->tutor_class = $id;
                $new_tutor->save();
            }
            $cls->name = $name;
            $cls->tutor = array($tutors[$id]);
            $cls->save();
        }
        session()->flash('success', '班級資料已更新並儲存！');
        return $this->classList();
    }

    public function subjectList()
    {
        $subjects = Subject::all();
        return view('admin.subjects', ['subjects' => $subjects]);
    }

    public function subjectUpdate(Request $request)
    {
        $subjects = $request->input('subjects');
        foreach ($subjects as $id => $name) {
            $subj = Subject::find($id);
            $subj->name = $name;
            $subj->save();
        }
        session()->flash('success', '科目名稱已更新並儲存！');
        return $this->subjectList();
    }

    public function teacherList(Request $request, $unit = '')
    {
        $units = Unit::main();
        if (empty($unit)) {
            $unit_id = $units->first()->id;
        } else {
            $unit_id = $unit;
        }
        $unit = Unit::find($unit_id);
        $keys = Unit::subkeys($unit->unit_no);
        $teachers = Teacher::whereIn('unit_id', $keys)->orderBy('realname')->get();
        return view('admin.teachers', ['current' => $unit_id, 'units' => $units, 'teachers' => $teachers]);
    }

    public function teacherEdit(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $units = Unit::orderBy('unit_no')->get();
        $roles = Role::orderBy('role_no')->get();
        $classes = Classroom::all();
        $subjects = Subject::all();
        $teacher = Teacher::with('roles')->with('units')->find($uuid);
        $assignment = DB::table('assignment')->where('uuid', $uuid)->get();
        return view('admin.teacheredit', ['referer' => $referer, 'teacher' => $teacher, 'units' => $units, 'roles' => $roles, 'assignment' => $assignment, 'classes' => $classes, 'subjects' => $subjects]);
    }
    
    public function teacherUpdate(Request $request, $uuid)
    {
        $teacher = Teacher::find($uuid);
        $new_roles = $request->input('roles');
        $old_roles = $teacher->roles;
        if (!empty($new_roles)) {
            $keywords = explode(',', config('services.tpedu.base_unit'));
            foreach ($new_roles as $new_id) {
                $ckf = false;
                $new = Role::find($new_id);
                foreach ($keywords as $k) {
                    if (!(mb_strpos($new->name, $k) === false)) {
                        $ckf = true;
                    }
                }
                if (!$ckf) {
                    $teacher->unit_id = $new->unit_id;
                    $teacher->unit_name = $new->unit->name;
                    $teacher->role_id = $new->id;
                    $teacher->role_name = $new->name;
                }
            }
            foreach ($old_roles as $old) {
                $pos = array_search($old->id, $new_roles);
                if ($pos !== false) {
                    unset($new_roles[$pos]);
                } else {
                    DB::table('job_title')->where('uuid', $uuid)->where('role_id', $old->id)->delete();
                }
            }    
        }
        if (!empty($new_roles)) {
            foreach ($new_roles as $role) {
                $new = Role::find($role);
                DB::table('job_title')->Insert([
                    'uuid' => $uuid,
                    'unit_id' => $new->unit_id,
                    'role_id' => $new->id,
                ]);
            }
        }
        $new_classes = $request->input('classes');
        $new_subjects = $request->input('subjects');
        $old_assign = $teacher->assignment();
        foreach ($old_assign as $old) {
            $found = false;
            foreach ($new_classes as $i => $nc) {
                if ($nc == $old->class_id && $new_subjects[$i] == $old->subject_id) {
                    $found = true;
                    unset($new_classes[$i]);
                    unset($new_subjects[$i]);
                }
            }
            if (!$found) {
                DB::table('assignment')->where('id', $old->id)->delete();
            } 
        }
        if (!empty($new_classes)) {
            for ($i=0; $i<count($new_classes); $i++) {
                DB::table('assignment')->Insert([
                    'uuid' => $uuid,
                    'class_id' => $new_classes[$i],
                    'subject_id' => $new_subjects[$i],
                ]);
            }
        }
        $characters = $request->input('character');
        if (!empty($characters)) {
            if (is_array($characters)) {
                $teacher->character = implode(',', $characters);
            } else {
                $teacher->character = $characters;
            }
        }
        $teacher->idno = $request->input('idno');
        $teacher->sn = $request->input('sn');
        $teacher->gn = $request->input('gn');
        $teacher->realname = $request->input('sn').$request->input('gn');
        $teacher->gender = $request->input('gender');
        $teacher->birthdate = $request->input('birth');
        $teacher->email = $request->input('email');
        $teacher->mobile = $request->input('mobile');
        $teacher->telephone = $request->input('telephone');
        $teacher->address = $request->input('address');
        $teacher->www = $request->input('www');
        $teacher->save();
        session()->flash('success', '教職員個人資料已更新儲存！');
        return redirect(urldecode($request->input('referer')));
    }
    
    public function studentList(Request $request, $myclass = '')
    {
        $classes = Classroom::all();
        if (empty($myclass)) {
            $class_id = $classes->first()->id;
        } else {
            $class_id = $myclass;
        }
        $students = Student::where('class_id', $class_id)->orderByRaw('cast(seat as unsigned)')->get();
        return view('admin.students', ['current' => $class_id, 'classes' => $classes, 'students' => $students]);
    }

    public function studentEdit(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $classes = Classroom::all();
        $student = Student::with('classroom')->find($uuid);
        return view('admin.studentedit', ['referer' => $referer, 'student' => $student, 'classes' => $classes]);
    }

    public function studentUpdate(Request $request, $uuid)
    {
        $student = Student::find($uuid);
        $characters = $request->input('character');
        if (!empty($character)) {
            $student->character = implode(',', $characters);
        }
        $student->idno = $request->input('idno');
        $student->sn = $request->input('sn');
        $student->gn = $request->input('gn');
        $student->realname = $request->input('sn').$request->input('gn');
        $student->gender = $request->input('gender');
        $student->birthdate = $request->input('birth');
        $student->class_id = $request->input('myclass');
        $student->seat = $request->input('seat');
        $student->email = $request->input('email');
        $student->mobile = $request->input('mobile');
        $student->telephone = $request->input('telephone');
        $student->address = $request->input('address');
        $student->www = $request->input('www');
        $student->save();
        session()->flash('success', '學生個人資料已更新儲存！');
        return redirect(urldecode($request->input('referer')));
    }

    public function menuList($menu = '')
    {
        $routename = [];
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) {
            $name = $value->getName();
            if (!empty($name)) $routename[] = $name;
        }
        $menus = Menu::parents();
        if (!empty($menu)) {
            $items = Menu::subItems($menu);
        } else {
            $items = Menu::top();
        }
        return view('admin.menus', ['current' => $menu, 'menus' => $menus, 'items' => $items, 'routes' => $routename]);
    }

    public function menuUpdate(Request $request, $menu = '')
    {
        $ids = $request->input('ids');
        $captions = $request->input('captions');
        $parents = $request->input('parents');
        $urls = $request->input('urls');
        $weights = $request->input('weights');
        foreach ($captions as $id => $title) {
            $m = Menu::find($id);
            $m->parent_id = $parents[$id];
            $m->caption = $title;
            $m->url = $urls[$id];
            $m->weight = $weights[$id];
            $m->save();
        }
        foreach ($ids as $old => $new) {
            if ($old == $new) continue;
            $m = Menu::find($old);
            $m->id = $new;
            $m->save();
        }
        session()->flash('success', '選單項目已經更新！');
        return $this->menuList($menu);
    }

    public function menuAdd($menu = '')
    {
        $routename = [];
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) {
            $name = $value->getName();
            if (!empty($name)) $routename[] = $name;
        }
        return view('admin.menuadd', ['current' => $menu, 'routes' => $routename]);
    }

    public function menuInsert(Request $request, $menu = '')
    {
        $mid = $request->input('mid');
        $caption = $request->input('caption');
        $url = $request->input('url');
        $weight = $request->input('weight');
        if (!empty($menu)) {
            Menu::create([
                'id' => $mid,
                'parent_id' => $menu,
                'caption' => $caption,
                'url' => $url,
                'weight' => $weight,
            ]);
        } else {
            Menu::create([
                'id' => $mid,
                'caption' => $caption,
                'url' => $url,
                'weight' => $weight,
            ]);
        }
        session()->flash('success', '選單項目新增完成！');
        return $this->menuList($menu);
    }

    public function menuDelete($menu)
    {
        $item = Menu::find($menu);
        $parent = $item->parent_id;
        if ($item->url == '#') {
            DB::table('menus')->where('parent_id', $menu)->update([
                'parent_id' => $parent,
            ]);    
        }
        $item->delete();
        session()->flash('success', '選單項目已經刪除！');
        return $this->menuList($parent);
    }

    public function permissionList()
    {
        $perms = Permission::orderBy('group')->get();
        return view('admin.permission', ['permission' => $perms]);
    }

    public function permissionAdd()
    {
        return view('admin.permissionadd');
    }

    public function permissionInsert(Request $request)
    {
        $app = $request->input('app');
        $perm = $request->input('perm');
        $desc = $request->input('desc');
        $ckf = Permission::findByName("$app.$perm");
        if ($ckf) {
            $request->flash();
            session()->flash('error', '該權限已經存在，無法再新增！');
            return back();
        }
        Permission::create([
            'group' => $app,
            'permission' => $perm,
            'description' => $desc,
        ]);
        session()->flash('success', '權限新增完成！');
        return $this->permissionList();
    }

    public function permissionEdit($id)
    {
        $perm = Permission::find($id);
        return view('admin.permissionedit', ['perm' => $perm]);
    }

    public function permissionUpdate(Request $request, $id)
    {
        $app = $request->input('app');
        $perm = $request->input('perm');
        $desc = $request->input('desc');
        $ckf = Permission::findByName("$app.$perm");
        if ($ckf && $ckf->id != $id) {
            $request->flash();
            session()->flash('error', '該權限已經存在，無法修改成新的代號！');
            return back();
        }
        Permission::find($id)->update([
            'group' => $app,
            'permission' => $perm,
            'description' => $desc,
        ]);
        session()->flash('success', '權限更新完成！');
        return $this->permissionList();
    }

    public function permissionRemove($id)
    {
        Permission::destroy($id);
        session()->flash('success', '權限已經移除！');
        return $this->permissionList();
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
        session()->flash('success', $log);
        return back();
    }

}
