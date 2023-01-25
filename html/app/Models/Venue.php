<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Carbon\Carbon;

class Venue extends Model
{

    protected $table = 'venues';

    protected static $weekMap = [
        0 => '週一',
        1 => '週二',
        2 => '週三',
        3 => '週四',
        4 => '週五',
    ];

    protected static $sessionMap = [
        0 => '早自習',
        1 => '第一節',
        2 => '第二節',
        3 => '第三節',
        4 => '第四節',
        5 => '午休',
        6 => '第五節',
        7 => '第六節',
        8 => '第七節',
        9 => '課後',
    ];

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'name',
        'uuid',
        'description',
        'availability',
        'unavailable_at',
        'unavailable_until',
        'schedule_limit',
        'open',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'sessions',
        'denytime',
        'denysession',
        'available',
        'manager',
        'reserved',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'availability' => AsCollection::class,
        'unavailable_at' => 'datetime:Y-m-d',
        'unavailable_until' => 'datetime:Y-m-d',
        'open' => 'boolean',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'sessions',
        'denytime',
        'denysession',
        'available',
    ];

    //提供所有節次陣列
    public function getSessionsAttribute()
    {
        return self::$sessionMap;
    }

    //提供不出借時段字串
    public function getDenytimeAttribute()
    {
        $str = '';
        if ($this->unavailable_at && $this->unavailable_until) {
            $str = substr($this->unavailable_at, 0, 10);
            $str .= '～';
            $str .= substr($this->unavailable_until, 0, 10);    
        }
        return $str;
    }

    //提供不出借節次字串
    public function getDenysessionAttribute()
    {
        $str = '';
        if ($this->availability) {
            for ($i=0; $i<5; $i++) {
                $session1 = '';
                if (!($this->available[$i][0]) && !($this->available[$i][1]) && !($this->available[$i][2]) &&
                    !($this->available[$i][3]) && !($this->available[$i][4]) && !($this->available[$i][5]) &&
                    !($this->available[$i][6]) && !($this->available[$i][7]) && !($this->available[$i][8]) &&
                    !($this->available[$i][9])) {
                    $session1 = '全天';
                    $session2 = '';
                } else {
                    $session1 = '';
                    if (!($this->available[$i][0]) && !($this->available[$i][1]) && !($this->available[$i][2]) &&
                          !($this->available[$i][3]) && !($this->available[$i][4])) {
                        $session1 = '早上';
                    } else {
                        for ($j=0; $j<5; $j++) {
                            if (!($this->available[$i][$j])) $session1 .= self::$sessionMap[$j].' ';
                        }
                    }
                    $session2 = '';
                    if (!($this->available[$i][5]) && !($this->available[$i][6]) && !($this->available[$i][7]) &&
                          !($this->available[$i][8]) && !($this->available[$i][9])) {
                        $session2 = '下午';
                    } else {
                        for ($j=5; $j<10; $j++) {
                            if (!($this->available[$i][$j])) $session2 .= self::$sessionMap[$j].' ';
                        }
                    }
                }
                if ($session1 || $session2) {
                    $str .= self::$weekMap[$i] . $session1 . (($session1 && $session2) ? ' ' : '') . $session2 . ' ';
                }
            }
        } else {
            $str = '尚未設定';
        }
        return $str;
    }

    //提供不出借節次陣列
    public function getAvailableAttribute()
    {
        $schedule = [];
        for ($i=0; $i<5; $i++) { // 0->星期一, 1->星期二,
            for ($j=0; $j<10; $j++) { // 0->早自習, 1->第一節, ......
                $found = true;
                if ($this->availability) {
                    foreach ($this->availability as $define) {
                        if ($define['weekday'] == $i && $define['session'] == $j) {
                            $found = false;
                            break;
                        }
                    }
                }
                $schedule[$i][$j] = $found;
            }
        }
        return $schedule;
    }

    //取得此場地的管理員
    public function manager()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此場地的預約記錄
    public function reserved()
    {
        return $this->hasMany('App\Models\VenueReserve');
    }

    //取得指定日期的週間預約記錄
    public function week_reserved(Carbon $date)
    {
        $sdate = $date->format('Y-m-d');
        $edate = $date->copy()->addDays(4)->format('Y-m-d');
        return $this->reserved()->whereBetween('reserved_at', [$sdate, $edate])->get();
    }

    //提供本週或指定日期已出借節次陣列
    public function weekly($date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } elseif (is_string($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        $sdate = $date->copy()->startOfWeek();
        $whole = new \stdClass;
        $whole->start = $sdate->copy(); //此週開始日期
        $whole->map = $this->available; //陣列元素為 true -> 可預約，false -> 不出借
        foreach ($this->week_reserved($sdate) as $b) {
            $whole->map[$b->weekday][$b->session] = $b; //已被預約，將預約記錄置入陣列中
            if ($b->length > 1) {
                for ($k=1; $k<$b->length; $k++) {
                    $whole->map[$b->weekday][$b->session + $k] = '-'; //此節課屬於已被預約的一部分，把這些節次設為 '-'
                }
            }
        }
        for ($i=0; $i<5; $i++) {
            if ($sdate->between($this->unavailable_at, $this->unavailable_until)) {
                for ($j=0; $j<10; $j++) {
                    if ($whole->map[$i][$j] === true) {
                        $whole->map[$i][$j] = false; //位於不出借時段，則設為 false
                    }
                }
            }
            if ($sdate <= Carbon::today()) {
                for ($j=0; $j<10; $j++) {
                    if ($whole->map[$i][$j] === true) {
                        $whole->map[$i][$j] = 'Z'; //如果時間已經結束，設為 'Z'
                    }
                }
            }
            if ($sdate > Carbon::today()->addDays($this->schedule_limit)) {
                for ($j=0; $j<10; $j++) {
                    $whole->map[$i][$j] = 'X'; //如果超過預約時程，設為 'X'
                }
            }
            $sdate->addDay();
        }
        return $whole;
    }

}
