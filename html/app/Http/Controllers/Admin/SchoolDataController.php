<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Domain;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Watchdog;

class SchoolDataController extends Controller
{
    public $year;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (date('m') > 7) {
            $year = date('Y') - 1911;
        } else {
            $year = date('Y') - 1912;
        }
        $this->year = $year;
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
                Watchdog::watch($request, '更新行政單位：' . $unit->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }
            $message ='行政單位已更新並儲存！';
        }
        if ($request->has('roles')) {
            $roles = $request->input('roles');
            $organize = $request->input('organize');
            $role_ids = $request->input('rid');
            foreach ($roles as $id => $name) {
                $role = Role::find($id);
                $role->role_no = $role_ids[$id];
                $role->name = $name;
                $role->organize = (isset($organize[$id]) && $organize[$id] == 'yes') ? true : false;
                $role->save();
                Watchdog::watch($request, '更新職務：' . $role->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }
            $message = '職稱已更新並儲存！';
        }
        $units = Unit::with('roles')->orderBy('unit_no')->get();
        return view('admin.units', ['units' => $units])->with('success', $message);
    }

    public function unitAdd()
    {
        return view('admin.unitadd');
    }

    public function unitInsert(Request $request)
    {
        $input = $request->only(['unit_id', 'unit_name']);
        if ($input) {
            $u = Unit::create([
                'unit_no' => $input['unit_id'],
                'name' => $input['unit_name'],
            ]);
            Watchdog::watch($request, '新增行政單位：' . $u->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        return redirect()->route('units')->with('success', '行政單位已新增完成！');
    }

    public function roleAdd()
    {
        $units = Unit::with('roles')->orderBy('unit_no')->get();
        return view('admin.roleadd', ['units' => $units]);
    }

    public function roleInsert(Request $request)
    {
        $r = Role::create([
            'role_no' => $request->input('role_id'),
            'unit_id' => $request->input('role_unit'),
            'name' => $request->input('role_name'),
            'organize' => $request->boolean('organize'),
        ]);
        Watchdog::watch($request, '更新職務：' . $r->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $units = Unit::with('roles')->orderBy('unit_no')->get();
        return view('admin.roleadd', ['units' => $units])->with('success', '職務層級已新增完成！');
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
            Watchdog::watch($request, '更新班級資訊：' . $cls->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        $grades = Grade::all();
        $classes = Classroom::all();
        $teachers = Teacher::orderBy('realname')->get();
        return view('admin.classes', ['grades' => $grades, 'classes' => $classes, 'teachers' => $teachers])->with('success', '班級資料已更新並儲存！');
    }

    public function domainList()
    {
        $domains = Domain::all();
        return view('admin.domains', ['domains' => $domains]);
    }

    public function domainUpdate(Request $request)
    {
        $domains = $request->input('domains');
        foreach ($domains as $id => $name) {
            $dom = Domain::find($id);
            $dom->name = $name;
            $dom->save();
            Watchdog::watch($request, '更新教學領域：' . $dom->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        return redirect()->route('domains')->with('success', '領域名稱已更新並儲存！');
    }

    public function domainAdd()
    {
        return view('admin.domainadd');
    }

    public function domainInsert(Request $request)
    {
        $input = $request->only(['domain_name']);
        if ($input) {
            $d = Domain::create([
                'name' => $input['domain_name'],
            ]);
            Watchdog::watch($request, '新增教學領域：' . $d->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        return redirect()->route('domains')->with('success', '教學領域已經新增完成！');
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
            Watchdog::watch($request, '更新教學科目：' . $subj->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        return redirect()->route('subjects')->with('success', '科目名稱已更新並儲存！');
    }

    public function teacherList($search = '')
    {
        $domain_id = $unit_id = $idno = $realname = $email = '';
        if (!empty($search)) {
            $parameters = explode('&', $search);
            foreach ($parameters as $p) {
                list($key, $val) = explode('=', $p);
                switch ($key) {
                    case 'unit':
                        $unit_id = $val;
                        break;
                    case 'domain':
                        $domain_id = $val;
                        break;
                    case 'idno':
                        $idno = $val;
                        break;
                    case 'name':
                        $realname = $val;
                        break;
                    case 'email':
                        $email = $val;
                        break;
                }
            }
        }
        $units = Unit::main();
        $domains = Domain::all();
        $query = Teacher::query();
        if (!empty($idno) || !empty($realname) || !empty($email)) {
            if (!empty($idno)) {
                $query = $query->where('idno', 'like', '%'.$idno.'%');
            }
            if (!empty($realname)) {
                $query = $query->where('realname', 'like', '%'.$realname.'%');
            }
            if (!empty($email)) {
                $query = $query->where('email', 'like', '%'.$email.'%');
            }
        } elseif (!empty($unit_id)) {
            $unit = Unit::find($unit_id);
            $keys = Unit::subkeys($unit->unit_no);
            $query = Teacher::whereIn('unit_id', $keys);
        } elseif (!empty($domain_id)) {
            $query = Teacher::leftJoin('belongs', 'belongs.uuid', 'teachers.uuid')->where('belongs.domain_id', $domain_id);
        } else {
            $unit_id = $units->first()->id;
            $unit = Unit::find($unit_id);
            $keys = Unit::subkeys($unit->unit_no);
            $query = Teacher::whereIn('unit_id', $keys);
        }
        $teachers = $query->orderBy('realname')->get()->sortBy(function ($t) {
            return $t->mainunit->id;
        });
        return view('admin.teachers', ['current' => $unit_id, 'domain' => $domain_id, 'idno' => $idno, 'realname' => $realname, 'email' => $email, 'units' => $units, 'domains' => $domains, 'teachers' => $teachers]);
    }

    public function teacherEdit(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $units = Unit::orderBy('unit_no')->get();
        $roles = Role::orderBy('role_no')->get();
        $classes = Classroom::all();
        $domains = Domain::all();
        $subjects = Subject::all();
        $teacher = Teacher::with('roles')->with('units')->find($uuid);
        $assignment = DB::table('assignment')->where('year', $this->year)->where('uuid', $uuid)->get();
        return view('admin.teacheredit', ['referer' => $referer, 'teacher' => $teacher, 'units' => $units, 'roles' => $roles, 'assignment' => $assignment, 'classes' => $classes, 'domains' => $domains, 'subjects' => $subjects]);
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
                    DB::table('job_title')->where('year', $this->year)->where('uuid', $uuid)->where('role_id', $old->id)->delete();
                }
            }
        }
        if (!empty($new_roles)) {
            foreach ($new_roles as $role) {
                $new = Role::find($role);
                DB::table('job_title')->insertOrIgnore([
                    'year' => $this->year,
                    'uuid' => $uuid,
                    'unit_id' => $new->unit_id,
                    'role_id' => $new->id,
                ]);
            }
        }
        $new_classes = $request->input('classes');
        DB::table('belongs')->where('year', $this->year)->where('uuid', $uuid)->delete();
        $new_domain = $request->input('domain');
        if ($new_domain) {
            DB::table('belongs')->insert([
                'year' => $this->year,
                'uuid' => $uuid,
                'domain_id' => $new_domain,
            ]);
        }
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
                DB::table('assignment')->insertOrIgnore([
                    'year' => $this->year,
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
        Watchdog::watch($request, '更新教師資訊：' . $teacher->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect(urldecode($request->input('referer')))->with('success', '教師資訊已經更新完成！');
    }

    public function teacherSync(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $t = Teacher::find($uuid);
        $t->sync();
        Watchdog::watch($request, '同步教師資訊：' . $t->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect(urldecode($referer))->with('success', '教師資訊已經重新同步！');
    }

    public function teacherRemove(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $t = Teacher::find($uuid);
        Watchdog::watch($request, '將離職教師標註為移除：' . $t->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $t->delete();
        return redirect(urldecode($referer))->with('success', '離職教師已經標註為移除！');
    }

    public function studentList($search = '')
    {
        $class_id = $idno = $id = $realname = $email = '';
        if (!empty($search)) {
            $parameters = explode('&', $search);
            foreach ($parameters as $p) {
                list($key, $val) = explode('=', $p);
                switch ($key) {
                    case 'class':
                        $class_id = $val;
                        break;
                    case 'id':
                        $id = $val;
                        break;
                    case 'idno':
                        $idno = $val;
                        break;
                    case 'name':
                        $realname = $val;
                        break;
                    case 'email':
                        $email = $val;
                        break;
                }
            }
        }
        $classes = Classroom::all();
        $query = Student::query();
        if (!empty($idno) || !empty($id) || !empty($realname) || !empty($email)) {
            if (!empty($idno)) {
                $query = $query->where('idno', 'like', '%'.$idno.'%');
            }
            if (!empty($id)) {
                $query = $query->where('id', 'like', '%'.$id.'%');
            }
            if (!empty($realname)) {
                $query = $query->where('realname', 'like', '%'.$realname.'%');
            }
            if (!empty($email)) {
                $query = $query->where('email', 'like', '%'.$email.'%');
            }
        } elseif (!empty($class_id)) {
            $query = $query->where('class_id', $class_id);
        } else {
            $class_id = $classes->first()->id;
            $query = $query->where('class_id', $class_id);
        }
        $students = $query->orderByRaw('class_id, cast(seat as unsigned)')->get();
        return view('admin.students', ['current' => $class_id, 'idno' => $idno, 'id' => $id, 'realname' => $realname, 'email' => $email, 'classes' => $classes, 'students' => $students]);
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
        Watchdog::watch($request, '更新學生資訊：' . $student->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect(urldecode($request->input('referer')))->with('success', '學生資訊已經更新完成！');
    }

    public function studentSync(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $s = Student::find($uuid);
        $s->sync();
        Watchdog::watch($request, '同步學生資訊：' . $s->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return redirect(urldecode($referer))->with('success', '學生資訊已經重新同步！');
    }

    public function studentRemove(Request $request, $uuid)
    {
        $referer = $request->headers->get('referer');
        $s = Student::find($uuid);
        Watchdog::watch($request, '將轉學生標註為移除：' . $s->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $s->delete();
        return redirect(urldecode($referer))->with('success', '轉學生已經標註為移除！');
    }

}
