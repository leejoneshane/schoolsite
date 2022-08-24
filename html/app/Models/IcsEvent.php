<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\IcalendarGenerator\Components\Event;
use Carbon\Carbon;
use App\Models\IcsCalendar;

class IcsEvent extends Model
{

    protected $table = 'ics_events';

    protected $fillable = [
        'unit_id', 'all_day', 'start', 'end', 'summary', 'description', 'location', 'calendar_id', 'event_id',
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'start' => 'date',
        'end' => 'date',
    ];

    public static function inTime($date)
    {
        return IcsEvent::whereDate('start', '<=', $date)->whereDate('end', '>=', $date)->get();
    }

    public static function inTimeForStudent($date)
    {
        $cal_id = IcsCalendar::forStudent()->id;
        return IcsEvent::where('calendar_id', $cal_id)->whereDate('start', '<=', $date)->whereDate('end', '>=', $date)->get();
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
            ->organizer(config('services.gsuite.calendar'), $this->unit()->name)
            ->createdAt(Carbon::createFromTimestamp($this->created_at, env('TZ')))
            ->startsAt(Carbon::createFromTimestamp($this->start, env('TZ')))
            ->endsAt(Carbon::createFromTimestamp($this->end, env('TZ')));
        if (!empty($this->description)) $event->description($this->description);
        if (!empty($this->location)) $event->addressName($this->location);
        if ($this->all_day) $event->fullDay();

        return $event;
    }

}