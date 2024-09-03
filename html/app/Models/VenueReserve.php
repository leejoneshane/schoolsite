<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueReserve extends Model
{

    protected $table = 'venue_reserved';

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
        'venue_id',
        'uuid',
        'teacher_name',
        'reserved_at',
        'weekday',
        'session',
        'length',
        'reason',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'venue',
        'subscriber',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'reserved_at' => 'datetime:Y-m-d',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'timesection',
    ];

    //提供預約時段中文字串
    public function getTimesectionAttribute()
    {
        $str = self::$weekMap[$this->weekday] . self::$sessionMap[$this->session];
        if ($this->length > 1) {
            $end = $this->session + $this->length -1;
            $str .= '到' . self::$sessionMap[$end];
        }
        return $str;
    }

    //取得此預約記錄要預約的場地
    public function venue()
    {
        return $this->belongsTo('App\Models\Venue');
    }

    //取得此預約紀錄的預約者
    public function subscriber()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

}
