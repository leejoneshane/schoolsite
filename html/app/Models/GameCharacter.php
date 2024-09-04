<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameCharacter extends Model
{

    protected $table = 'game_characters';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'face_no',
        'body_no',
        'profession',
        'level',
        'xp',
        'hp',
        'mp',
        'ap',
        'dp',
        'sp',
        'gp',
        'items',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'items' => 'array',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'student',
        'parties',
        'teammate',
    ];

    //取得此角色的學生物件
    public function student()
    {
        return $this->hasOne('App\Models\Student', 'uuid', 'uuid')->withDefault();
    }

    //取得此角色的隊伍物件
    public function parties()
    {
        return GameParty::whereIN('members', $this->uuid)->all();
    }

    //取得此角色的夥伴
    public function teammate($party_id)
    {
        return GameParty::find($party_id)->teammate();
    }

    //檢查此角色是否可升級，若可以則進行升級
    public function levelup()
    {
    }

    //檢查此角色是否有可學習但尚未學習的技能
    public function learnable()
    {
    }

}
