<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GameMonsterSpawn;

class GameMonster extends Model
{

    protected $table = 'game_monsters';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',        //怪物種族名稱
        'description', //怪物種族簡介
        'min_level',   //怪物起始等級
        'max_level',   //怪物最高等級
        'hp',          //最大健康值
        'crit_rate',   //爆擊率，爆擊時攻擊力為基本攻擊力的 2 倍
        'ap',          //基本攻擊力
        'dp',          //基本防禦力
        'sp',          //基本敏捷力
        'xp',          //打敗怪物可獲得經驗值
        'gp',          //打敗怪物可獲得金幣
        'style',         //難易度色碼
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'images',
        'skills',
    ];

    //取得此怪物的圖片
    public function images()
    {
        return $this->morphMany('App\Models\GameImage', 'owner');
    }

    //取得此怪物的技能
    public function skills()
    {
        return $this->belongsToMany('App\Models\GameSkill', 'game_monsters_skills', 'monster_id', 'skill_id')->withPivot(['level'])->orderByPivot('level');
    }

    //隨機選取怪物圖片網址
    public function random_url()
    {
        return $this->images->random()->url();
    }

    //隨機選取怪物圖片網址
    public function spawn($uuid)
    {
        $rnd = mt_rand()/mt_getrandmax();
        $level = intval($this->min_level + $rnd * ($this->max_level - $this->min_level));
        $max_hp = intval($this->hp + $this->hp * $rnd / 2);
        $ap = intval($this->ap + $this->ap * $rnd / 2);
        $dp = intval($this->dp + $this->dp * $rnd / 2);
        $sp = intval($this->sp + $this->sp * $rnd / 2);
        $url = $this->random_url();
        GameMonsterSpawn::where('uuid', $uuid)->delete();
        $spawn = GameMonsterSpawn::create([
            'uuid' => $uuid,
            'monster_id' => $this->id,
            'name' => 'L' . $level . ' ' . $this->name,
            'level' => $level,
            'url' => $url,
            'max_hp' => $max_hp,
            'hp' => $max_hp,
            'crit_rate' => $this->crit_rate,
            'ap' => $ap,
            'dp' => $dp,
            'sp' => $sp,
            'xp' => $this->xp,
            'gp' => $this->gp,
        ]);
        return $spawn;
    }

}
