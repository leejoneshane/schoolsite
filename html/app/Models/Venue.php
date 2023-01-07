<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Carbon\Carbon;

class Venue extends Model
{

    protected $table = 'venues';

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

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'availability' => AsCollection::class,
        'unavailable_at' => 'datetime:Y-m-d',
        'unavailable_until' => 'datetime:Y-m-d',
        'open' => 'boolean',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'denytime',
        'available',
    ];

    //提供禁止預約時段字串
    public function getDenytimeAttribute()
    {
        $str = substr($this->unavailable_at, 0, 10);
        $str .= '～';
        $str .= substr($this->unavailable_until, 0, 10);
        return ($str == '～') ? '' : $str;
    }

    //提供不出借節次陣列
    public function getAvailableAttribute()
    {
        $schedule = [];
        for ($i=0; $i<5; $i++) { // 0->星期一, 1->星期二,
            for ($j=0; $j<6; $j++) { // 0->早自習, 1->第一節, ......
                $found = true;
                if ($this->availability) {
                    $found = $this->availability->contains(function ($define) use ($i, $j) {
                        return !($define->weekday == $i && $define->session == $j);
                    });
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
    public function week_reserved(Carbon $sdate)
    {
        $edate = $sdate->addDays(4);
        return $this->reserved()->whereBetween('reserved_at', [$sdate->format('Y-m-d'), $edate->format('Y-m-d')])->get();
    }

    //提供本週或指定日期已出借節次陣列
    public function weekly($date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } elseif (is_string($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        $sdate = $date->startOfWeek();
        $whole = new \stdClass;
        $whole->start = $sdate; //此週開始日期
        $whole->weekday = $this->available;
        foreach ($this->week_reserved($sdate) as $b) {
            $whole->weekday[$b->weekday][$b->session] = $b; 
        }
        return $whole;
    }
}
