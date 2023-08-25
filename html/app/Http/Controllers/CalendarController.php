<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;
use App\Models\Teacher;
use App\Models\IcsCalendar;
use App\Models\IcsEvent;
use App\Providers\GcalendarServiceProvider as GCAL;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Watchdog;

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
        if (!$today) $today = $request->old('current');
        if (!$today) $today = date('Y-m-d');
        $current = Carbon::parse($today, env('TZ'));
        $notyet = false;
        if ($current > Carbon::now()) $notyet = true;
        $create = false;
        $edit = [];
        $delete = [];
        if ($request->user()) {
            $create = $request->user()->can('create', IcsEvent::class);
            if ($request->user()->user_type == 'Student') {
                $calendar = IcsCalendar::forStudent();
                $events = IcsEvent::inTimeForStudent($today);
            } else {
                $calendar = IcsCalendar::main();
                $events = IcsEvent::inTime($today);
            }
            foreach ($events as $event) {
                $edit[$event->id] = $notyet && $request->user()->can('update', $event);
                $delete[$event->id] = $notyet && $request->user()->can('delete', $event);
            }
        } else {
            $calendar = IcsCalendar::forStudent();
            $events = IcsEvent::inTimeForStudent($today);
            foreach ($events as $event) {
                $edit[$event->id] = false;
                $delete[$event->id] = false;
            }
        }
        return view('app.calendar', ['create' => $create, 'calendar' => $calendar, 'current' => $current, 'events' => $events, 'editable' => $edit, 'deleteable' => $delete]);
    }

    public function eventAdd(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $seme = (object) array('mindate' => Carbon::tomorrow()->format('Y-m-d'), 'maxdate' => (date('Y')+1).'-12-31');
        $calendars = IcsCalendar::orderBy('seq')->get();
        $user = $request->user();
        $units = [];
        if ($user->is_admin) {
            $units = Unit::main();
            $default = '';
        }
        if ($user->user_type == 'Teacher') {
            $t = Teacher::find($user->uuid);
            $units = $t->upper();
            $default = $t->unit_id;
        }
        return view('app.calendar_eventadd', ['current' => $today, 'seme' => $seme, 'calendars' => $calendars, 'default' => $default, 'units' => $units]);
    }

    public function eventInsert(Request $request)
    {
        $event = IcsEvent::create([
            'uuid' => $request->user()->uuid,
            'unit_id' => $request->input('unit_id'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'summary' => $request->input('summary'),
            'description' => $request->input('desc'),
            'location' => $request->input('location'),
            'calendar_id' => $request->input('calendar_id'),
        ]);
        if ($request->has('important')) {
            $event->important = true;
        }
        if ($request->has('training')) {
            $event->training = true;
        }
        if ($request->has('all_day')) {
            $event->all_day = true;
        } else {
            $start = $request->input('start_time');
            if (strlen($start) == 5) $start .= ':00';
            $event->startTime = $start;
            $end = $request->input('end_time');
            if (strlen($end) == 5) $end .= ':00';
            $event->endTime = $end;
        }
        $event->save();
        Watchdog::watch($request, '新增行事曆事件：' . $event->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $link = route('calendar') . '?current=' . $request->input('current');
        return redirect($link);
    }

    public function eventEdit(Request $request, $event_id)
    {
        $event = IcsEvent::find($event_id);
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $seme = (object) array('mindate' => Carbon::tomorrow()->format('Y-m-d'), 'maxdate' => (date('Y')+1).'-12-31');
        $calendars = IcsCalendar::orderBy('seq')->get();
        $user = $request->user();
        $units = [];
        if ($user->is_admin) {
            $units = Unit::main();
        }
        if ($user->user_type == 'Teacher') {
            $t = Teacher::find($user->uuid);
            $units = $t->upper();
        }
        return view('app.calendar_eventedit', ['current' => $today, 'seme' => $seme, 'calendars' => $calendars, 'units' => $units, 'event' => $event]);
    }

    public function eventUpdate(Request $request, $event_id)
    {
        $event = IcsEvent::find($event_id);
        $event->fill([
            'unit_id' => $request->input('unit_id'),
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'summary' => $request->input('summary'),
            'description' => $request->input('desc'),
            'location' => $request->input('location'),
            'calendar_id' => $request->input('calendar_id'),
        ]);
        if ($request->has('important')) {
            $event->important = true;
        }
        if ($request->has('training')) {
            $event->training = true;
        }
        if ($request->has('all_day')) {
            $event->all_day = true;
        } else {
            $event->all_day = false;
            $start = $request->input('start_time');
            if (strlen($start) == 5) $start .= ':00';
            $event->startTime = $start;
            $end = $request->input('end_time');
            if (strlen($end) == 5) $end .= ':00';
            $event->endTime = $end;
        }
        $event->save();
        Watchdog::watch($request, '更新行事曆事件：' . $event->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $link = route('calendar') . '?current=' . $request->input('current');
        return redirect($link);
    }

    public function eventRemove(Request $request, $event_id)
    {
        $e = IcsEvent::find($event_id);
        Watchdog::watch($request, '移除行事曆事件：' . $e->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $e->delete();
        $link = route('calendar') . '?current=' . $request->input('current');
        return redirect($link);
    }

    public function seme(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $section = $request->input('section');
        if (!$section) {
            $year = current_year();
            $seme = current_seme();
            $section = $year . $seme;
        } else {
            $year = (integer) substr($section, 0, -1);
            $seme = (integer) substr($section, -1);
        }
        if ($seme == 1) {
            $y = $year+1911;
            $month = [
                (object) ['y' => $y, 'm' => 8],
                (object) ['y' => $y, 'm' => 9],
                (object) ['y' => $y, 'm' => 10],
                (object) ['y' => $y, 'm' => 11],
                (object) ['y' => $y, 'm' => 12],
                (object) ['y' => $y + 1, 'm' => 1],
            ];
            $prev = ($year - 1).'2';
            $next = $year.'2';
        } else {
            $y = $year+1912;
            $month = [
                (object) ['y' => $y, 'm' => 2],
                (object) ['y' => $y, 'm' => 3],
                (object) ['y' => $y, 'm' => 4],
                (object) ['y' => $y, 'm' => 5],
                (object) ['y' => $y, 'm' => 6],
                (object) ['y' => $y, 'm' => 7],
            ];
            $prev = $year.'1';
            $next = ($year + 1).'1';
        }
        $event_list = [];
        foreach ($month as $t) {
            $min = Carbon::parse($t->y.'-'.$t->m)->startOfMonth();
            $max = Carbon::parse($t->y.'-'.$t->m)->endOfMonth();
            $period = CarbonPeriod::create($min, $max);
            foreach ($period as $sd) {
                $events = IcsEvent::inTime($sd);
                $important = $events->where('important', true);
                $events = $events->where('important', false);
                $content = '';
                if ($important->count() > 0) $content .= '【學校重要活動】';
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
                        $content .= '【'.$uname.'】';
                        $last = $e->unit_id;
                    }
                    $content .= '　'.$e->summary;
                    if (!empty($e->location)) $content .= ' 地點：'.$e->location;
                    if (!($e->all_day)) $content .= ' 時間：'.$e->startTime.'到'.$e->endTime;
                    if ($e->startDate != $e->endDate) $content .= '(至'.$e->endDate->format('Y-m-d').'止)';
                }
                if ($content) {
                    $obj = new \stdClass;
                    $obj->month = self::$monthMap[$t->m];
                    $obj->day = $sd->day;
                    $obj->weekday = self::$weekMap[$sd->dayOfWeek];
                    $obj->content = $content;
                    $event_list[] = $obj;    
                }
            }
        }
        return view('app.calendar_seme', ['current' => $today, 'section' => $section, 'prev' => $prev, 'next' => $next, 'events' => $event_list]);
    }

    public function training(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $section = $request->input('section');
        if (!$section) {
            $year = current_year();
            $seme = current_seme();
            $section = $year . $seme;
        } else {
            $year = (integer) substr($section, 0, -1);
            $seme = (integer) substr($section, -1);
        }
        if ($seme == 1) {
            $y = $year+1911;
            $month = [
                (object) ['y' => $y, 'm' => 8],
                (object) ['y' => $y, 'm' => 9],
                (object) ['y' => $y, 'm' => 10],
                (object) ['y' => $y, 'm' => 11],
                (object) ['y' => $y, 'm' => 12],
                (object) ['y' => $y + 1, 'm' => 1],
            ];
            $prev = ($year - 1).'2';
            $next = $year.'2';
        } else {
            $y = $year+1912;
            $month = [
                (object) ['y' => $y, 'm' => 2],
                (object) ['y' => $y, 'm' => 3],
                (object) ['y' => $y, 'm' => 4],
                (object) ['y' => $y, 'm' => 5],
                (object) ['y' => $y, 'm' => 6],
                (object) ['y' => $y, 'm' => 7],
            ];
            $prev = $year.'1';
            $next = ($year + 1).'1';
        }
        $event_list = [];
        foreach ($month as $t) {
            $min = Carbon::parse($t->y.'-'.$t->m)->startOfMonth();
            $max = Carbon::parse($t->y.'-'.$t->m)->endOfMonth();
            $period = CarbonPeriod::create($min, $max);
            foreach ($period as $sd) {
                $events = IcsEvent::inTimeForTraining($sd);
                $important = $events->where('important', true);
                $events = $events->where('important', false);
                $content = '';
                if ($important->count() > 0) $content .= '【學校重要活動】';
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
                        $content .= '【'.$uname.'】';
                        $last = $e->unit_id;
                    }
                    $content .= '　'.$e->summary;
                    if (!empty($e->location)) $content .= ' 地點：'.$e->location;
                    if (!($e->all_day)) $content .= ' 時間：'.$e->startTime.'到'.$e->endTime;
                    if ($e->startDate != $e->endDate) $content .= '(至'.$e->endDate->format('Y-m-d').'止)';
                }
                if ($content) {
                    $obj = new \stdClass;
                    $obj->month = self::$monthMap[$t->m];
                    $obj->day = $sd->day;
                    $obj->weekday = self::$weekMap[$sd->dayOfWeek];
                    $obj->content = $content;
                    $event_list[] = $obj;    
                }
            }
        }
        return view('app.calendar_training', ['current' => $today, 'section' => $section, 'prev' => $prev, 'next' => $next, 'events' => $event_list]);
    }

    public function student(Request $request)
    {
        $today = $request->input('current');
        if (!$today) $today = date('Y-m-d');
        $section = $request->input('section');
        if (!$section) {
            $year = current_year();
            $seme = current_seme();
            $section = $year . $seme;
        } else {
            $year = (integer) substr($section, 0, -1);
            $seme = (integer) substr($section, -1);
        }
        if ($seme == 1) {
            $y = $year+1911;
            $month = [
                (object) ['y' => $y, 'm' => 8],
                (object) ['y' => $y, 'm' => 9],
                (object) ['y' => $y, 'm' => 10],
                (object) ['y' => $y, 'm' => 11],
                (object) ['y' => $y, 'm' => 12],
                (object) ['y' => $y + 1, 'm' => 1],
            ];
            $prev = ($year - 1).'2';
            $next = $year.'2';
        } else {
            $y = $year+1912;
            $month = [
                (object) ['y' => $y, 'm' => 2],
                (object) ['y' => $y, 'm' => 3],
                (object) ['y' => $y, 'm' => 4],
                (object) ['y' => $y, 'm' => 5],
                (object) ['y' => $y, 'm' => 6],
                (object) ['y' => $y, 'm' => 7],
            ];
            $prev = $year.'1';
            $next = ($year + 1).'1';
        }
        $event_list = [];
        foreach ($month as $t) {
            $min = Carbon::parse($t->y.'-'.$t->m)->startOfMonth();
            $max = Carbon::parse($t->y.'-'.$t->m)->endOfMonth();
            $period = CarbonPeriod::create($min, $max);
            foreach ($period as $sd) {
                $events = IcsEvent::inTimeForStudent($sd);
                $important = $events->where('important', true);
                $events = $events->where('important', false);
                $content = '';
                if ($important->count() > 0) $content .= '【學校重要活動】';
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
                        $content .= '【'.$uname.'】';
                        $last = $e->unit_id;
                    }
                    $content .= '　'.$e->summary;
                    if (!empty($e->location)) $content .= ' 地點：'.$e->location;
                    if (!($e->all_day)) $content .= ' 時間：'.$e->startTime.'到'.$e->endTime;
                    if ($e->startDate != $e->endDate) $content .= '(至'.$e->endDate->format('Y-m-d').'止)';
                }
                if ($content) {
                    $obj = new \stdClass;
                    $obj->month = self::$monthMap[$t->m];
                    $obj->day = $sd->day;
                    $obj->weekday = self::$weekMap[$sd->dayOfWeek];
                    $obj->content = $content;
                    $event_list[] = $obj;    
                }
            }
        }
        return view('app.calendar_student', ['current' => $today, 'section' => $section, 'prev' => $prev, 'next' => $next, 'events' => $event_list]);
    }

    public function download(Request $request)
    {
        if (Auth::check() && $request->user()->user_type == 'Student') {
            $calendar = IcsCalendar::forStudent();
        } else {
            $calendar = IcsCalendar::main();
        }
        return $calendar->download();
    }

    public function import(Request $request)
    {
        if (Auth::check() && $request->user()->user_type == 'Student') {
            $calendar = IcsCalendar::forStudent();
        } else {
            $calendar = IcsCalendar::main();
        }
        return $calendar->stream();
    }

}
