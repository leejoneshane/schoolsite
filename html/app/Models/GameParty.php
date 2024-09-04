<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameParty extends Model
{

    protected $table = 'game_parties';

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id',
        'uuid',
        'name',
        'sence_no',
        'effect_hp',
        'effect_mp',
        'effect_ap',
        'effect_dp',
        'effect_sp',
        'treasury',
        'furnitures',
        'members',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'teammate',
        'classroom',
        'teacher',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'furnitures' => 'array',
        'members' => 'array',
    ];

    //取得此隊伍的角色物件
    public function teammate()
    {
        return GameCharacter::whereIn('uuid', $this->members)->all();
    }

    //取得此隊伍的所屬班級
    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom');
    }

    //取得此隊伍的指導教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
