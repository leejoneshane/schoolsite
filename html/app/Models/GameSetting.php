<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSetting extends Model
{

    protected $table = 'game_settings';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',          //教師 UUID
        'description',   //行為描述
        'type',          //正向行為 positive，負面行為 negative
        'effect_xp',     //對學生的經驗值增減，2 則加 2 點，-2 則扣 2 點，套用前由老師自行修改
        'effect_gp',     //對學生的金幣增減
        'effect_item',   //獎勵道具
        'effect_hp',     //對學生的健康增減
        'effect_mp',     //對學生的行動力增減
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'teacher',
        'item',
    ];

    //取得設定此遊戲規則的教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得設定此遊戲規則的教師
    public function item()
    {
        return $this->hasOne('App\Models\GameItem', 'id', 'effect_item');
    }

    //取得指定教師設定的獎勵條款
    public static function positive($uuid)
    {
        return GameSetting::where('uuid', $uuid)->where('type', 'positive')->get();
    }

    //取得指定教師設定的獎勵條款
    public static function negative($uuid)
    {
        return GameSetting::where('uuid', $uuid)->where('type', 'negative')->get();
    }

}
