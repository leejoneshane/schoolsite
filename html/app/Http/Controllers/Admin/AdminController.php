<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Watchdog;

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
        }
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
        if (!Menu::find('domains')) {
            Menu::create([
                'id' => 'domains',
                'parent_id' => 'database',
                'caption' => '教學領域',
                'url' => 'route.domains',
                'weight' => 70,
            ]);
        }
        if (!Menu::find('subjects')) {
            Menu::create([
                'id' => 'subjects',
                'parent_id' => 'database',
                'caption' => '學習科目',
                'url' => 'route.subjects',
                'weight' => 80,
            ]);
        }
        if (!Menu::find('teachers')) {
            Menu::create([
                'id' => 'teachers',
                'parent_id' => 'database',
                'caption' => '教職員',
                'url' => 'route.teachers',
                'weight' => 90,
            ]);
        }
        if (!Menu::find('students')) {
            Menu::create([
                'id' => 'students',
                'parent_id' => 'database',
                'caption' => '學生',
                'url' => 'route.students',
                'weight' => 100,
            ]);
        }
        if (!Menu::find('website')) {
            Menu::create([
                'id' => 'website',
                'parent_id' => 'admin',
                'caption' => '網站組態管理',
                'url' => '#',
                'weight' => 20,
            ]);
        }
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
        if (!Menu::find('newsletter')) {
            Menu::create([
                'id' => 'newsletter',
                'parent_id' => 'website',
                'caption' => '電子報管理',
                'url' => 'route.news',
                'weight' => 30,
            ]);
        }
        if (!Menu::find('watchdog')) {
            Menu::create([
                'id' => 'watchdog',
                'parent_id' => 'website',
                'caption' => '瀏覽歷程',
                'url' => 'route.watchdog',
                'weight' => 40,
            ]);
        }
    }

    public function index()
    {
        return view('admin');
    }

}
