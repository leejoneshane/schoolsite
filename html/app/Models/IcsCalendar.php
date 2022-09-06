<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\IcalendarGenerator\Components\Calendar;

class IcsCalendar extends Model
{
    protected $table = 'ics_calendars';
	protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'summary',
    ];

    public static function forStudent()
    {
        return IcsCalendar::where('summary', 'like', '%學生%')->first();
    }

    public static function main()
    {
        return IcsCalendar::find(config('services.gsuite.calendar'));
    }

    public function events()
    {
        return $this->hasMany('App\Models\IcsEvent', 'calendar_id', 'id');
    }

    public function url()
    {
        return 'https://calendar.google.com/calendar/embed?src='.urlencode($this->id).'&ctz=Asia%2FTaipei';
    }

    public function toICS()
    {
        $calendar = Calendar::create($this->summary);
        foreach ($this->events as $event) {
            $event = $calendar->event($event->toICS());
        }
        
        return $calendar;
    }

    public function stream()
    {
        $calendar = $this->toICS()->get();
        return response($calendar)->header('Content-Type', 'text/calendar; charset=utf-8');
    }

    public function download()
    {
        $calendar = $this->toICS()->get();
        return response($calendar, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'. $this->summary .'行事曆.ics"',
         ]);
    }

}