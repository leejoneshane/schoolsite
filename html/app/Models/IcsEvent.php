<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\IcalendarGenerator\Components\Event;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\IcsCalendar;

class IcsEvent extends Model
{

    protected $table = 'ics_events';

    protected $fillable = [
        'unit_id', 'startDate', 'endDate', 'all_day', 'startTime', 'endTime', 'summary', 'description', 'location', 'calendar_id', 'event_id',
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'startDate' => 'datetime:Y-m-d',
        'endDate' => 'datetime:Y-m-d',
        'startTime' => 'datetime:H:i:s',
        'endTime' => 'datetime:H:i:s',
    ];

    public static function inTime($date)
    {
        return IcsEvent::with('unit')->whereDate('startDate', '<=', $date)->whereDate('endDate', '>=', $date)->get();
    }

    public static function inTimeForStudent($date)
    {
        $cal_id = IcsCalendar::forStudent()->id;
        return IcsEvent::with('unit')->where('calendar_id', $cal_id)->whereDate('startDate', '<=', $date)->whereDate('endDate', '>=', $date)->get();
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
        $start = Carbon::createFromFormat('Y-m-d', $this->startDate, env('TZ'));
        $end = Carbon::createFromFormat('Y-m-d', $this->endDate, env('TZ'));
        $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate.' '.$this->startTime, env('TZ'));
        $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate.' '.$this->endTime, env('TZ'));
        if ($this->all_day) {
            $event->startsAt($start)->fullDay();
        } else {
            $event->period($start_time, $end_time);
        }
        if ($start->toDateString() != $end->toDateString()) {
            $days = [];
            $period = CarbonPeriod::create($start->addDay(), $end);
            foreach ($period as $date) {
                $days[] = new DateTime($date->format('Y-m-d').' '.$this->startTime);
            }
            $event->repeatOn($days); 
        }
        if (!empty($this->description)) $event->description($this->description);
        if (!empty($this->location)) $event->addressName($this->location);

        return $event;
    }

}