<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!Menu::find('main')) {
            Menu::create([
                'id' => 'main',
                'caption' => '主選單',
                'url' => '#',
                'weight' => 0,
            ]);
        }
        if (!Menu::find('parenting')) {
            Menu::create([
                'id' => 'parenting',
                'parent_id' => 'main',
                'caption' => '親師服務',
                'url' => '#',
                'weight' => 10,
            ]);
        }
        if (!Menu::find('affairs')) {
            Menu::create([
                'id' => 'affairs',
                'parent_id' => 'main',
                'caption' => '行政服務',
                'url' => '#',
                'weight' => 20,
            ]);
        }
        if (!Menu::find('classmanage')) {
            Menu::create([
                'id' => 'classmanage',
                'parent_id' => 'main',
                'caption' => '班級經營',
                'url' => '#',
                'weight' => 30,
            ]);
        }
        if (!Menu::find('calendar')) {
            Menu::create([
                'id' => 'calendar',
                'parent_id' => 'parenting',
                'caption' => '學校行事曆',
                'url' => 'route.calendar',
                'weight' => 10,
            ]);
        }
        if (!Menu::find('club')) {
            Menu::create([
                'id' => 'club',
                'parent_id' => 'parenting',
                'caption' => '學生課外社團',
                'url' => 'route.clubs',
                'weight' => 20,
            ]);
        }
        if (!Menu::find('lunch')) {
            Menu::create([
                'id' => 'lunch',
                'parent_id' => 'parenting',
                'caption' => '午餐調查',
                'url' => 'route.lunch',
                'weight' => 30,
            ]);
        }
        if (!Menu::find('writing')) {
            Menu::create([
                'id' => 'writing',
                'parent_id' => 'parenting',
                'caption' => '明日小作家',
                'url' => 'route.writing',
                'weight' => 40,
            ]);
        }
        if (!Menu::find('subscriber')) {
            Menu::create([
                'id' => 'subscriber',
                'parent_id' => 'parenting',
                'caption' => '訂閱電子報',
                'url' => 'route.subscriber',
                'weight' => 50,
            ]);
        }
        if (!Menu::find('meeting')) {
            Menu::create([
                'id' => 'meeting',
                'parent_id' => 'affairs',
                'caption' => '網路朝會',
                'url' => 'route.meeting',
                'weight' => 10,
            ]);
        }
        if (!Menu::find('seniority')) {
            Menu::create([
                'id' => 'seniority',
                'parent_id' => 'affairs',
                'caption' => '年資統計',
                'url' => 'route.seniority',
                'weight' => 20,
            ]);
        }
        if (!Menu::find('organize')) {
            Menu::create([
                'id' => 'organize',
                'parent_id' => 'affairs',
                'caption' => '職編系統',
                'url' => 'route.organize',
                'weight' => 30,
            ]);
        }
        if (!Menu::find('venue')) {
            Menu::create([
                'id' => 'venue',
                'parent_id' => 'affairs',
                'caption' => '場地預約',
                'url' => 'route.venues',
                'weight' => 40,
            ]);
        }
        if (!Menu::find('repair')) {
            Menu::create([
                'id' => 'repair',
                'parent_id' => 'affairs',
                'caption' => '修繕登記',
                'url' => 'route.repair',
                'weight' => 50,
            ]);
        }
        if (!Menu::find('roster')) {
            Menu::create([
                'id' => 'roster',
                'parent_id' => 'affairs',
                'caption' => '學生名單填報',
                'url' => 'route.rosters',
                'weight' => 60,
            ]);
        }
        if (!Menu::find('dayoff')) {
            Menu::create([
                'id' => 'dayoff',
                'parent_id' => 'affairs',
                'caption' => '公假單',
                'url' => 'route.dayoff',
                'weight' => 70,
            ]);
        }
        if (!Menu::find('seats')) {
            Menu::create([
                'id' => 'seats',
                'parent_id' => 'classmanage',
                'caption' => '分組座位表',
                'url' => 'route.seats',
                'weight' => 10,
            ]);
        }
    }

    public function index()
    {
        return view('home');
    }

}
