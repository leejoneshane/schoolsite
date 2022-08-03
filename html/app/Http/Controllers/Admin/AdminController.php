<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Jobs\SyncFromTpedu;
use App\Providers\TpeduServiceProvider as SSO;

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
                'caption' => '資料同步',
                'url' => 'route.sync',
                'weight' => 10,
            ]);
            Menu::create([
                'id' => 'units',
                'parent_id' => 'database',
                'caption' => '行政單位與職稱',
                'url' => 'route.units',
                'weight' => 20,
            ]);
            Menu::create([
                'id' => 'jobs',
                'parent_id' => 'database',
                'caption' => '職務編排',
                'url' => 'route.jobs',
                'weight' => 30,
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
    public function index()
    {
        return view('admin');
    }

    public function syncFromTpedu()
    {
        $this->dispatch(new SyncFromTpedu);
        session()->flash('success', '同步作業已經在背景執行，當同步作業完成時，您將接獲電子郵件通知！與此同時，您可以先進行其他工作或直接關閉網頁！');
        return view('admin');
    }

    public function units()
    {
        return view('admin');
    }

    public function jobs()
    {
        return view('admin');
    }

    public function classes()
    {
        return view('admin');
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
