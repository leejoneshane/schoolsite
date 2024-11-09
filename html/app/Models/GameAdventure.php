<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAdventure extends Model
{

    protected $table = 'game_adventures';

    //以下屬性可以批次寫入
    protected $fillable = [
        'syear',        //學年
        'uuid',         //指派者
        'classroom_id', //指派班級
        'worksheet_id', //學習單編號
        'open',         //是否開放
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'teacher',
        'classroom',
        'worksheet',
        'processes',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'open' => 'boolean',
    ];

    //自動移除所有學習任務
    protected static function booted()
    {
        self::deleting(function($item)
        {
            foreach ($item->processes as $p) {
                $p->delete();
            }
        });
    }

    //篩選指定教師指派的所有地圖探險
    public static function findByUuid($uuid)
    {
        return GameAdventure::where('syear', current_year())->where('uuid', $uuid)->get();
    }

    //篩選指定班級的開放中的探險地圖
    public static function findByClassroom($room_id)
    {
        return GameAdventure::where('syear', current_year())->where('classroom_id', $room_id)->where('open', true)->first();
    }

    //篩選指定學習單的所有地圖探險
    public static function findByWorksheet($worksheet_id)
    {
        return GameAdventure::where('syear', current_year())->where('worksheet_id', $worksheet_id)->get();
    }

    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'id', 'classroom_id');
    }

    public function worksheet()
    {
        return $this->hasOne('App\Models\GameWorksheet', 'id', 'worksheet_id');
    }

    public function processes()
    {
        return $this->hasMany('App\Models\GameProcess', 'adventure_id');
    }

}
