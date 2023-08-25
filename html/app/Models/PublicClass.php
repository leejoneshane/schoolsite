<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;

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
        1 => (object)[ 'start' => '8:45', 'end' => '9:25' ],
        2 => (object)[ 'start' => '9:35', 'end' => '10:15' ],
        3 => (object)[ 'start' => '10:30', 'end' => '11:10' ],
        4 => (object)[ 'start' => '11:20', 'end' => '12:00' ],
        5 => (object)[ 'start' => '12:40', 'end' => '13:20' ],
        6 => (object)[ 'start' => '13:30', 'end' => '14:10' ],
        7 => (object)[ 'start' => '14:20', 'end' => '15:00' ],
        8 => (object)[ 'start' => '15:20', 'end' => '16:00' ],
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
        'place',
        'uuid',
        'partners',
        'eduplan',
        'discuss',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'domain',
        'classroom',
        'teacher',
        'eduplan',
        'discuss',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'reserved_at' => 'datetime:Y-m-d',
        'partners' => 'array',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'weeksession',
        'timeperiod',
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
    }

    //提供公開課節次中文字串
    public function getWeeksessionAttribute()
    {
        return self::$weekMap[$this->weekday] . self::$sessionMap[$this->session];
    }

    //提供公開課時段字串
    public function getTimeperiodAttribute()
    {
        $period = self::$sessionTime[$this->session];
        return $this->reserved_at . $period->start . '~' . $period->end;
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
}
