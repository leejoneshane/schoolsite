<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameConfigure extends Model
{

    protected $table = 'game_configures';

    //以下屬性可以批次寫入
    protected $fillable = [
        'syear',
        'classroom_id',
        'daily_mp',          //每日回復行動力
        'change_base',       //允許小組重選據點
        'change_class',      //允許學生更換職業
        'arena_open',        //開啟競技場（小組對戰功能）
        'furniture_shop',    //開放家具店
        'item_shop',         //開放道具店
        'pet_shop',          //開放寵物店（預留）
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
    ];

    //篩選指定學年、班級的組態
    public static function findByClass($room_id)
    {
        return GameConfigure::where('syear', current_year())->where('classroom_id', $room_id)->first();
    }

    //取得設定此組態適用班級
    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom');
    }

}
