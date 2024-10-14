<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMonster extends Model
{

    protected $table = 'game_monsters';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',        //怪物名稱
        'description', //怪物簡介
        'max_hp',      //最大健康值
        'hp',          //目前健康值
        'hit_rate',    //攻擊命中率
        'crit_rate',   //爆擊率，爆擊時攻擊力為基本攻擊力的 2 倍
        'ap',          //基本攻擊力
        'dp',          //基本防禦力
        'sp',          //基本敏捷力
        'xp',          //打敗怪物可獲得經驗值
        'gp',          //打敗怪物可獲得金幣
        'temp_effect',   //暫時增益:ap,mp,sp 其中之一
        'effect_value',  //增益值，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'effect_timeout',//增益結束時間，timestamp
        'style',         //難易度色碼
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'images',
    ];

    //取得此怪物的圖片
    public function images()
    {
        return $this->morphMany('App\Models\GameImage', 'owner');
    }

}
