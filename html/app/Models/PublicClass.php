<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Providers\GcalendarServiceProvider as GCAL;
use App\Models\IcsCalendar;
use Carbon\Carbon;

class PublicClass extends Model
{

    protected $table = 'public_class';

    protected static $weekMap = [
        1 => '週一',
        2 => '週二',
        3 => '週三',
        4 => '週四',
        5 => '週五',
    ];

    protected static $sessionMap = [
        1 => '第一節',
        2 => '第二節',
        3 => '第三節',
        4 => '第四節',
        5 => '午休',
        6 => '第五節',
        7 => '第六節',
        8 => '第七節',
    ];

    protected static $sessionTime = [
        1 => [ 'start' => '08:45', 'end' => '09:25' ],
        2 => [ 'start' => '09:35', 'end' => '10:15' ],
        3 => [ 'start' => '10:30', 'end' => '11:10' ],
        4 => [ 'start' => '11:20', 'end' => '12:00' ],
        5 => [ 'start' => '12:40', 'end' => '13:20' ],
        6 => [ 'start' => '13:30', 'end' => '14:10' ],
        7 => [ 'start' => '14:20', 'end' => '15:00' ],
        8 => [ 'start' => '15:20', 'end' => '16:00' ],
    ];

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'section',
        'domain_id',
        'teach_unit',
        'teach_grade',
        'teach_class',
        'reserved_at',
        'weekday',
        'session',
        'location',
        'uuid',
        'partners',
        'eduplan',
        'discuss',
        'event_id',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'domain',
        'classroom',
        'teacher',
        'eduplan',
        'discuss',
        'event_id',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'reserved_at' => 'datetime:Y-m-d',
        'partners' => 'array',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'week_session',
        'timeperiod',
        'period',
        'summary',
    ];

    //建立公開課時，若省略學期，則預設為目前學期
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->section)) {
                $model->section = current_section();
            }
        });
        static::created(function($item)
        {
            $cal = new GCAL;
            $cal->sync_public($item);
        });
        static::updated(function($item)
        {
            $cal = new GCAL;
            $cal->sync_public($item);
        });
        static::deleted(function($item)
        {
            $calendar_id = IcsCalendar::forPublic()->id;
            $cal = new GCAL;
            if ($item->event_id) {
                $cal->delete_event($calendar_id, $item->event_id);
            }
        });
    }

    //提供公開課節次中文字串
    public function getWeekSessionAttribute()
    {
        return self::$weekMap[$this->weekday] . self::$sessionMap[$this->session];
    }

    //提供公開課時段字串
    public function getTimeperiodAttribute()
    {
        $period = self::$sessionTime[$this->session];
        return $this->reserved_at->format('Y-m-d') . ' ' . $period['start'] . '~' . $period['end'];
    }

    //提供公開課時段
    public function getPeriodAttribute()
    {
        return self::$sessionTime[$this->session];
    }

    //提供公開課摘要
    public function getSummaryAttribute()
    {
        $summary = $this->teacher->realname . '老師公開課（';
        $summary .= $this->classroom->name . $this->domain->name . $this->teach_unit . $this->week_session . $this->location . '）';
        return $summary;
    }

    //取得此公開課的教學領域
    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }

    //取得此公開課的授課班級
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'teach_class');
    }

    //取得此公開課的任教老師
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此公開課的觀課老師
    public function teachers()
    {
        return Teacher::whereIn('uuid', $this->partners)->get();
    }

    //篩選指定學期所有公開課紀錄
    public static function sections()
    {
        return PublicClass::query()->selectRaw('DISTINCT(section)')->orderBy('section', 'desc')->get()->map(function ($item) {
            $sec = $item->section;
            $seme = substr($sec, -1);
            if ($seme == 1) {
                $strseme = '上學期';
            } else {
                $strseme = '下學期';
            }
            return (object) [ 'section' => $sec, 'name' => '第'.substr($sec, 0, -1).'學年'.$strseme ];
        });
    }

    //篩選指定學期所有公開課紀錄
    public static function bySection($section = null)
    {
        if (!$section) $section = current_section();
        return PublicClass::where('section', $section)->get();
    }

    //篩選指定學期，指定領域所有教師的公開課紀錄
    public static function byDomain($domain_id, $section = null)
    {
        if (!$section) $section = current_section();
        return PublicClass::where('section', $section)->where('domain_id', $domain_id)->get();
    }

    //篩選指定學期，指定教師的所有公開課紀錄
    public static function byUser($uuid, $section = null)
    {
        if (!$section) $section = current_section();
        return PublicClass::where('section', $section)->where('uuid', $uuid)->get();
    }

    //取得指定日期的週間預約記錄
    public static function week_reserved(Carbon $date)
    {
        $sdate = $date->copy()->startOfWeek();
        $edate = $date->copy()->addDays(6)->format('Y-m-d');
        return PublicClass::whereBetween('reserved_at', [$sdate, $edate])->get();
    }

    //提供本週或指定日期公開課節次陣列
    public static function weekly($date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } elseif (is_string($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        $sdate = $date->copy()->startOfWeek();
        $whole = new \stdClass;
        $whole->start = $sdate; //此週開始日期
        $whole->today = Carbon::today();
        for ($i=1; $i<6; $i++) { // 1->星期一, 2->星期二, .....
            for ($j=1; $j<9; $j++) { // 1->第一節, 2->第二節, ......
                $whole->map[$i][$j] = [];
            }
        }
        foreach (self::week_reserved($date) as $b) {
            $whole->map[$b->weekday][$b->session][] = $b; //已被預約，將預約記錄置入陣列中
        }
        return $whole;
    }

}
