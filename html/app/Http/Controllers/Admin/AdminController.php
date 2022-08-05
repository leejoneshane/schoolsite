<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Jobs\SyncFromTpedu;
use App\Providers\TpeduServiceProvider as SSO;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;

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
            Menu::create([
                'id' => 'sync',
                'parent_id' => 'database',
                'caption' => '更新快取資料',
                'url' => 'route.sync',
                'weight' => 10,
            ]);
            Menu::create([
                'id' => 'forcesync',
                'parent_id' => 'database',
                'caption' => '強制資料同步',
                'url' => 'route.forcesync',
                'weight' => 10,
            ]);
            Menu::create([
                'id' => 'units',
                'parent_id' => 'database',
                'caption' => '行政單位與職權',
                'url' => 'route.units',
                'weight' => 20,
            ]);
            Menu::create([
                'id' => 'classes',
                'parent_id' => 'database',
                'caption' => '年級與班級',
                'url' => 'route.classes',
                'weight' => 40,
            ]);
            Menu::create([
                'id' => 'subjects',
                'parent_id' => 'database',
                'caption' => '學習科目',
                'url' => 'route.subjects',
                'weight' => 50,
            ]);
            Menu::create([
                'id' => 'assignment',
                'parent_id' => 'database',
                'caption' => '課務安排',
                'url' => 'route.assignment',
                'weight' => 60,
            ]);
            Menu::create([
                'id' => 'teachers',
                'parent_id' => 'database',
                'caption' => '教職員',
                'url' => 'route.teachers',
                'weight' => 70,
            ]);
            Menu::create([
                'id' => 'students',
                'parent_id' => 'database',
                'caption' => '學生',
                'url' => 'route.students',
                'weight' => 80,
            ]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('admin');
    }

    public function syncFromTpedu()
    {
        SyncFromTpedu::dispatch(true);
        session()->flash('success', '此同步作業僅同步超過'.config('services.tpedu.expired_days').'天的資料，同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
        return view('admin');
    }

    public function forceSyncFromTpedu()
    {
        SyncFromTpedu::dispatch(false);
        session()->flash('success', '同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
        return view('admin');
    }

    public function unitList()
    {
        $units = Unit::with('roles')->get();
        return view('admin.units', ['units' => $units]);
    }

    public function unitUpdate(Request $request)
    {
        foreach ($request->all() as $k => $i) {
            if ($k == '_token') continue;
            $a = explode('_', $k);
            if (isset($a[1])) {
                $role = Role::find($a[1]);
                $role->name = $i;
                $role->save();
            } else {
                $unit = Unit::find($a[0]);
                $unit->name = $i;
                $unit->save();
            }
        }
        return $this->unitList();
    }

    public function classList()
    {
        $grades = Grade::all();
        $classes = Classroom::all();
        $teachers = Teacher::all();
        return view('admin.classes', ['grades' => $grades, 'classes' => $classes, 'teachers' => $teachers]);
    }

    public function classUpdate(Request $request)
    {
        $names = $request->input('name');
        $tutors = $request->input('tutor');
        foreach ($names as $k => $i) {
            $cls = Classroom::find($k);
            $cls->name = $i;
            $cls->tutor = array($tutors[$k]);
            $cls->save();
        }
        return $this->classList();
    }

    public function subjects()
    {
        return view('admin');
    }

    public function assignment()
    {
        return view('admin');
    }

    public function teachers()
    {
        return view('admin');
    }

    public function students()
    {
        return view('admin');
    }

}
