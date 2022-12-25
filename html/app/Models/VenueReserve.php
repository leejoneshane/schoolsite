<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueReserve extends Model
{

    protected $table = 'venue_reserved';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'venue_id',
        'subscriber',
        'reserved_at',
        'weekday',
        'session',
        'length',
        'reason',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'reserved_at' => 'datetime:Y-m-d',
    ];

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
