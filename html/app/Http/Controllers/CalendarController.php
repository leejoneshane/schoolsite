<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Teacher;
use App\Models\IcsCalendar;
use App\Models\IcsEvent;
use App\Providers\GcalendarServiceProvider as GCAL;
use Carbon\Carbon;

class CalendarController extends Controller
{
    protected static $monthMap = [
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
        7 => '七',
        8 => '八',
        9 => '九',
        10 => '十',
        11 => '十一',
        12 => '十二',
    ];
    protected static $weekMap = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $check_data = IcsCalendar::first();
        if (is_null($check_data)) {
            $cal = new GCAL;
            $cal->list_calendars();
        }
    }

    public function calendar(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $seme = GCAL::current_seme();
        $create = false;
        $edit = [];
        $delete = [];
        if ($request->user()) {
            $create = $request->user()->can('create', IcsEvent::class);
            if ($request->user()->user_type == 'Student') {
                $events = IcsEvent::inTimeForStudent($today);
            } else {
                $events = IcsEvent::inTime($today);
            }
            foreach ($events as $event) {
                $edit[$event->id] = $request->user()->can('update', $event);
                $delete[$event->id] = $request->user()->can('delete', $event);
            }
        } else {
            $events = IcsEvent::inTimeForStudent($today);
            foreach ($events as $event) {
                $edit[$event->id] = false;
                $delete[$event->id] = false;
            }
        }
        return view('app.calendar', ['create' => $create, 'current' => $today, 'seme' => $seme, 'events' => $events, 'editable' => $edit, 'deleteable' => $delete]);
    }

    public function eventAdd(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $seme = GCAL::current_seme();
        $calendars = IcsCalendar::all();
        $user = $request->user();
        $units = [];
        if ($user->is_admin) {
            $units = Unit::main();
            $default = '';
        }
        if ($user->user_type == 'Teacher') {
            $t = Teacher::find($user->uuid);
            $units = $t->units;
            $default = $t->unit_id;
        }
        return view('app.eventadd', ['current' => $today, 'seme' => $seme, 'calendars' => $calendars, 'default' => $default, 'units' => $units]);
    }

    public function eventInsert(Request $request)
    {
        $event = IcsEvent::create([
            'unit_id' => $request->input('unit_id'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'summary' => $request->input('summary'),
            'description' => $request->input('desc'),
            'location' => $request->input('location'),
            'calendar_id' => $request->input('calendar_id'),
        ]);
        if ($request->has('all_day')) {
            $event->all_day = true;
        } else {
            $event->startTime = $request->input('start_time');
            $event->endTime = $request->input('end_time');
        }
        $event->save();
        return $this->calendar($request);
    }

    public function student(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $seme = GCAL::current_seme();
        $calendar = IcsCalendar::forStudent();
        $event_list = [];
        if ($seme['seme'] == 1) {
            $year = [ $seme['syear'], $seme['eyear'] ];
            $month = [ 8, 9, 10, 11, 12, 1 ];
        } else {
            $year = [ $seme['syear'] ];
            $month = [ 2, 3, 4, 5, 6, 7 ];
        }
        foreach ($year as $y) {
            foreach ($month as $m) {
                $min = 1;
                $max = Carbon::createFromFormat($y.'-'.$m.'-1')->endOfMonth()->day();
                for ($day = $min; $day <= $max; $day++) {                   
                    $sd = new Carbon($y.'-'.$m.'-'.$day);
                    $events = IcsEvent::inTimeForStudent($sd);
                    $important = $events->where('important', true);
                    $events = $events->where('important', false);
                    $content = '';
                    if ($important->count() > 0) $content .= '[學校重要活動]';
                    foreach ($important as $i) {
                        $content .= '　'.$i->summary;
                        if (!empty($i->location)) $content .= ' 地點：'.$i->location;
                        if (!($i->all_day)) $content .= ' 時間：'.$i->startTime.'到'.$i->endTime;
                        if ($i->startDate != $i->endDate) $content .= '(至'.$i->endDate.'止)';
                    }
                    $last = '';
                    foreach ($events as $e) {
                        if ($last != $e->unit_id) {
                            $uname = Unit::find($e->unit_id)->name;
                            $content .= "[$uname]";
                            $last = $e->unit_id;
                        }
                        $content .= '　'.$e->summary;
                        if (!empty($e->location)) $content .= ' 地點：'.$e->location;
                        if (!($e->all_day)) $content .= ' 時間：'.$e->startTime.'到'.$e->endTime;
                        if ($e->startDate != $e->endDate) $content .= '(至'.$e->endDate.'止)';
                    }
                    if ($content) {
                        $obj = new \stdClass;
                        $obj->month = $this->monthMap[$m];
                        $obj->weekday = $this->weekMap[$sd->dayOfWeek];
                        $obj->content = $content;
                        $event_list[$day] = $obj;    
                    }
                }
            }
        }
        return view('app.calendar_student', ['events' => $event_list]);
    }
}
