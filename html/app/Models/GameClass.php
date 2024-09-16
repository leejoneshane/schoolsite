<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameClass extends Model
{

    protected $table = 'game_classes';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',      //職業名稱
        'description', //職業簡介
        'hp_lvlup',  //健康值升級比率，3 則每次升級加 1~3 點，0.5 則有 1/2 機率加 1 點 
        'mp_lvlup',  //行動力升級比率
        'ap_lvlup',  //攻擊力升級比率
        'dp_lvlup',  //防禦力升級比率
        'sp_lvlup',  //敏捷力升級比率
        'base_hp',   //第一級時的基本健康值
        'base_mp',   //第一級時的基本行動力
        'base_ap',   //第一級時的基本攻擊力
        'base_dp',   //第一級時的基本防禦力
        'base_sp',   //第一級時的基本敏捷力
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'skills',
        'images',
    ];

    //取得此職業的技能
    public function skills()
    {
        return $this->belongsToMany('App\Models\GameSkill', 'game_classes_skills', 'class_id', 'skill_id')->withPivot(['level'])->orderByPivot('level');
    }

    //取得此職業的圖片
    public function images()
    {
        return $this->morphMany('App\Models\GameImage', 'owner');
    }

}
