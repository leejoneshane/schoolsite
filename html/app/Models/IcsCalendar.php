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

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'summary',
        'seq',
    ];

    //篩選出主要行事曆
    public static function main()
    {
        return IcsCalendar::find(config('services.gsuite.calendar'));
    }

    //篩選出學生行事曆
    public static function forStudent()
    {
        return IcsCalendar::where('summary', 'like', '%學生%')->first();
    }

    //篩選出公開課行事曆
    public static function forTeacher()
    {
        return IcsCalendar::where('summary', 'like', '%公開課%')->first();
    }

    //取得此行事曆所有事件
    public function events()
    {
        return $this->hasMany('App\Models\IcsEvent', 'calendar_id', 'id');
    }

    //取得此行事曆對應的 Google 連結
    public function url()
    {
        return 'https://calendar.google.com/calendar/ical/'.urlencode($this->id).'/public/basic.ics';
    }

    //將此行事曆轉換為標準 ICS 格式
    public function toICS()
    {
        $calendar = Calendar::create($this->summary);
        foreach ($this->events as $event) {
            $event = $calendar->event($event->toICS());
        }
        return $calendar;
    }

    //取得此行事曆線上串流
    public function stream()
    {
        $calendar = $this->toICS()->get();
        return response($calendar)->header('Content-Type', 'text/calendar; charset=utf-8');
    }

    //下載此行事曆
    public function download()
    {
        $calendar = $this->toICS()->get();
        return response($calendar, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'. $this->summary .'.ics"',
         ]);
    }

}