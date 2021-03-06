<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\IcalendarGenerator\Components\Event;
use Carbon\Carbon;

class IcsEvent extends Model
{

    protected $table = 'ics_events';

    protected $fillable = [
        'unit_id', 'all_day', 'start', 'end', 'summary', 'description', 'location', 'calendar_id', 'event_id',
    ];

    protected $casts = [
        'all_day' => 'boolean',
    ];

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
        $event = Event::create(self::$summary)
            ->organizer(config('services.gsuite.calendar'), $this->unit()->name)
            ->createdAt(Carbon::createFromTimestamp(self::$created_at, env('TZ')))
            ->startsAt(Carbon::createFromTimestamp(self::$start, env('TZ')))
            ->endsAt(Carbon::createFromTimestamp(self::$end, env('TZ')));
        if (!empty(self::description)) $event->description(self::description);
        if (!empty(self::location)) $event->addressName(self::location);
        if (self::$all_day) $event->fullDay();

        return $event;
    }

}