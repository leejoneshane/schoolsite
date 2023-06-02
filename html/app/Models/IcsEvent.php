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

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'unit_id',
        'all_day',
        'important',
        'startDate',
        'endDate',
        'startTime',
        'endTime',
        'summary',
        'description',
        'location',
        'calendar_id',
        'event_id',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'calendar',
        'unit',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'all_day' => 'boolean',
        'important' => 'boolean',
        'training' => 'boolean',
        'startDate' => 'datetime:Y-m-d',
        'endDate' => 'datetime:Y-m-d',
    ];

    //建立、更新、刪除行事曆事件時，同步到 Gsuite 中
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

    //取得要輸出到電子報的本月份行事曆內容
    public function newsletter()
    {
        $year = date('Y') - 1911;
        $month = date('n');
        $twmonth = self::$monthMap[$month];
        $event_list = [];
        $min = 1;
        $max = (new Carbon('last day of this month'))->day;
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

    //篩選指定日期的所有行事曆事件，靜態函式
    public static function inTime($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return IcsEvent::with('unit')->whereDate('startDate', '<=', $dt)->whereDate('endDate', '>=', $dt)->get();
    }

    //篩選指定日期的所有學生行事曆事件，靜態函式
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

    //篩選指定日期的所有研習行事曆事件，靜態函式
    public static function inTimeForTraining($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return IcsEvent::with('unit')->where('training', true)->whereDate('startDate', '<=', $dt)->whereDate('endDate', '>=', $dt)->get();
    }

    //篩選本月份的所有學生行事曆事件，靜態函式
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

    //取得此事件的建立者
    public function creater()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得登載此事件的行事曆物件
    public function calendar()
    {
        return $this->belongsTo('App\Models\IcsCalendar', 'id', 'calendar_id');
    }

    //取得此事件的登錄單位
    public function unit()
    {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    //將此行事曆事件轉換為標準 ICS 格式
    public function toICS()
    {
        $event = Event::create($this->summary)
            ->organizer(config('services.gsuite.calendar'), $this->unit->name)
            ->createdAt(Carbon::createFromTimestamp($this->updated_at, env('TZ')));
        if ($this->all_day) {
            $event->startsAt($this->startDate)->fullDay();
        } else {
            $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate->format('Y-m-d').' '.$this->startTime, env('TZ'));
            $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $this->startDate->format('Y-m-d').' '.$this->endTime, env('TZ'));
            $event->period($start_time, $end_time);
        }
        if ($this->startDate->format('Y-m-d') != $this->endDate->format('Y-m-d')) {
            $days = [];
            $nextday = $this->startDate;
            $nextday = $nextday->addDay();
            $period = CarbonPeriod::create($nextday, $this->endDate);
            foreach ($period as $date) {
                if ($this->all_day) {
                    $days[] = Carbon::createFromFormat('Y-m-d', $date->format('Y-m-d'), env('TZ'));
                } else {
                    $days[] = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.$this->startTime, env('TZ'));
                }
            }
            $event->repeatOn($days); 
        }
        if (!empty($this->description)) $event->description($this->description);
        if (!empty($this->location)) $event->addressName($this->location);

        return $event;
    }

}