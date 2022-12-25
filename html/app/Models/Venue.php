<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Venue extends Model
{

    protected $table = 'venues';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'name',
        'manager',
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

}
