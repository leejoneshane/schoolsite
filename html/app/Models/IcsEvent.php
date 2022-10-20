<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\IcalendarGenerator\Components\Event;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Interfaces\Subscribeable;
use App\Models\IcsCalendar;
use App\Providers\GcalendarServiceProvider as GCAL;

class IcsEvent extends Model implements Subscribeable
{

    protected $table = 'ics_events';
    const template = 'emails.calendar';

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

    protected $fillable = [
        'unit_id', 'all_day', 'important', 'startDate', 'endDate', 'startTime', 'endTime', 'summary', 'description', 'location', 'calendar_id', 'event_id',
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'important' => 'boolean',
        'training' => 'boolean',
        'startDate' => 'datetime:Y-m-d',
        'endDate' => 'datetime:Y-m-d',
    ];

    public static function boot()
    {
        parent::boot();
        static::created(function($item)
        {
            $cal = new GCAL;
            $cal->sync_event($item);
        });
        static::updated(function($item)
        {
            $cal = new GCAL;
            $cal->sync_event($item);
        });
        static::deleted(function($item)
        {
            $cal = new GCAL;
            if ($item->event_id) {
                $cal->delete_event($item->calendar_id, $item->event_id);
            }
        });
    }

    public function newsletter()
    {
        $year = date('Y') - 1911;
        $month = date('n');
        $twmonth = self::$monthMap[$month];
        $event_list = [];
        $min = 1;
        $max = (new Carbon('last day of this month'))->day();
        for ($day = $min; $day <= $max; $day++) {
            $obj = new \stdClass;
            $sd = new Carbon($year.'-'.$month.'-'.$day);
            $wd = self::$weekMap[$sd->dayOfWeek];
            $obj->weekday = $wd;
            $events = self::inTimeForStudent($sd);
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
            $obj->content = $content;
            $event_list[$day] = $obj;
        }
        return ['year' => $year, 'month' => $twmonth, 'events' => $event_list];
    }

    public static function inTime($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return IcsEvent::with('unit')->whereDate('startDate', '<=', $dt)->whereDate('endDate', '>=', $dt)->get();
    }

    public static function inTimeForStudent($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        $cal = IcsCalendar::forStudent();
        if ($cal) $cal_id = $cal->id;
        return IcsEvent::with('unit')->where('calendar_id', $cal_id)->whereDate('startDate', '<=', $dt)->whereDate('endDate', '>=', $dt)->get();
    }

    public static function inTimeForTraining($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return IcsEvent::with('unit')->where('training', true)->whereDate('startDate', '<=', $dt)->whereDate('endDate', '>=', $dt)->get();
    }

    public static function inMonthForStudent()
    {
        $cal = IcsCalendar::forStudent();
        if ($cal) $cal_id = $cal->id;
        $min = (new Carbon('first day of this month'))->toDateString();
        $max = (new Carbon('last day of this month'))->toDateString();
        return IcsEvent::with('unit')
            ->whereRaw('calendar_id = ? and ((startDate >= ? and startDate <= ?) or (endDate >= ? and endDate <= ?))', [$cal_id, $min, $max, $min, $max])
            ->get();
    }

    public function calendar()
    {
        return $this->belongsTo('App\Models\IcsCalendar', 'id', 'calendar_id');
    }

    public function unit()
    {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    public function toICS()
    {
        $event = Event::create($this->summary)
            ->organizer(config('services.gsuite.calendar'), $this->unit->name)
            ->createdAt(Carbon::createFromTimestamp($this->updated_at, env('TZ')));
        $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate->toDateString().' '.$this->startTime, env('TZ'));
        $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate->toDateString().' '.$this->endTime, env('TZ'));
        if ($this->all_day) {
            $event->startsAt($this->startDate)->fullDay();
        } else {
            $event->period($start_time, $end_time);
        }
        if ($this->startDate->toDateString() != $this->endDate->toDateString()) {
            $days = [];
            $nextday = $this->startDate;
            $nextday = $nextday->addDay();
            $period = CarbonPeriod::create($nextday, $this->endDate);
            foreach ($period as $date) {
                $days[] = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.$this->startTime);
            }
            $event->repeatOn($days); 
        }
        if (!empty($this->description)) $event->description($this->description);
        if (!empty($this->location)) $event->addressName($this->location);

        return $event;
    }

}