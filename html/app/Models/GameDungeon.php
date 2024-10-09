<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameDungeon extends Model
{

    protected $table = 'game_dungeons';

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id', //施測班級
        'evaluate_id',  //評量代號
        'monster_id',   //配置的怪物
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'evaluate',
        'monster',
    ];

    //取得此地下城的測驗卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此地下城的班級
    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'id', 'classroom_id');
    }

    //取得此地下城的怪物
    public function monster()
    {
        return $this->hasOne('App\Models\GameMonster', 'id', 'monster_id');
    }

}
