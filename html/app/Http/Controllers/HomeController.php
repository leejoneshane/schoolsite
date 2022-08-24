<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\IcsCalendar;
use App\Models\IcsEvent;
use App\Providers\GcalendarServiceProvider as GCAL;
use App\Policies\IcsEventPolicy;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!Menu::find('calendar')) {
            Menu::create([
                'id' => 'calendar',
                'parent_id' => 'main',
                'caption' => '學校行事曆',
                'url' => 'route.calendar',
                'weight' => 10,
            ]);
        }
    }

    public function index()
    {
        return view('home');
    }

    public function calendar(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $seme = GCAL::current_seme();
        $create = false;
        if ($request->user()) {
            $create = $request->user()->can('create', IcsEvent::class);
            if ($request->user()->user_type == 'Student') {
                $events = IcsEvent::inTimeForStudent($today);
            } else {
                $events = IcsEvent::inTime($today);
            }
        }
        return view('app.calendar', ['create' => $create, 'current' => $today, 'seme' => $seme, 'events' => $events]);
    }

}
