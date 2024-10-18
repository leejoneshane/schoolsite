<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameMonsterSpawn extends Model
{

    protected $table = 'game_monster_spawns';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',        //戰鬥對象
        'monster_id',  //怪物種類
        'name',        //怪物名稱
        'level',       //怪物等級
        'url',         //圖片網址
        'max_hp',      //最大健康值
        'hp',          //目前健康值
        'crit_rate',   //爆擊率，爆擊時攻擊力為基本攻擊力的 2 倍
        'ap',          //基本攻擊力
        'dp',          //基本防禦力
        'sp',          //基本敏捷力
        'xp',          //打敗怪物可獲得經驗值
        'gp',          //打敗怪物可獲得金幣
        'temp_effect',   //暫時增益:ap,dp,sp 其中之一
        'effect_value',  //增益值，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'effect_timeout',//增益結束時間，timestamp
        'buff',          //減益狀態
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'final_ap',
        'final_dp',
        'final_sp',
        'status',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'monster',
        'skills',
    ];

    //更新角色時，自動刪除
    protected static function booted()
    {
        self::updating(function ($item) {
            if ($item->hp < 1) $item->hp = 0;
            if ($item->hp > $item->max_hp) $item->hp = $item->max_hp;
        });
    }

    //提供 AP 計算結果
    public function getFinalApAttribute()
    {
        $ap = $this->ap;
        if ($this->temp_effect == 'ap') {
            if (Carbon::now() < $this->effect_timeout) {
                if ($this->effect_value > 1 || $this->effect_value < -1) {
                    $ap += $this->effect_value;
                } else {
                    $ap += intval($this->ap * $this->effect_value);
                }
            } else {
                $this->temp_effect = null;
                $this->effect_value = 0;
                $this->effect_timeout = null;
            }
        }
        if ($this->buff == 'weak') {
            if (Carbon::now() < $this->effect_timeout) {
                $ap = intval($ap * 0.5);
            } else {
                $this->buff = null;
                $this->effect_timeout = null;
                $this->save();
            }
        }
        return $ap;
    }

    //提供 DP 計算結果
    public function getFinalDpAttribute()
    {
        $dp = $this->dp;
        if ($this->temp_effect == 'dp') {
            if (Carbon::now() < $this->effect_timeout) {
                if ($this->effect_value > 1 || $this->effect_value < -1) {
                    $dp += $this->effect_value;
                } else {
                    $dp += intval($this->dp * $this->effect_value);
                }
            } else {
                $this->temp_effect = null;
                $this->effect_value = 0;
                $this->effect_timeout = null;
            }
        }
        if ($this->buff == 'weak') {
            if (Carbon::now() < $this->effect_timeout) {
                $dp = intval($dp * 0.5);
            } else {
                $this->buff = null;
                $this->effect_timeout = null;
                $this->save();
            }
        }
        return $dp;
    }

    //提供 SP 計算結果
    public function getFinalSpAttribute()
    {
        $sp = $this->sp;
        if ($this->temp_effect == 'sp') {
            if (Carbon::now() < $this->effect_timeout) {
                if ($this->effect_value > 1 || $this->effect_value < -1) {
                    $sp += $this->effect_value;
                } else {
                    $sp += intval($this->sp * $this->effect_value);
                }
            } else {
                $this->temp_effect = null;
                $this->effect_value = 0;
                $this->effect_timeout = null;
            }
        }
        if ($this->buff == 'weak') {
            if (Carbon::now() < $this->effect_timeout) {
                $sp = intval($sp * 0.5);
            } else {
                $this->buff = null;
                $this->effect_timeout = null;
                $this->save();
            }
        }
        return $sp;
    }

    //提供怪物狀態
    public function getStatusAttribute()
    {
        if ($this->hp < 1) return '死亡';
        if ($this->buff == 'invincible') return '無敵狀態';
        if ($this->buff == 'hatred') return '集中仇恨';
        if ($this->buff == 'protect') return '護衛';
        if ($this->buff == 'protected') return '被保護';
        if ($this->buff == 'reflex') return '傷害反射';
        if ($this->buff == 'apportion') return '分散傷害';
        if ($this->buff == 'weak') return '身體虛弱';
        if ($this->buff == 'paralysis') return '精神麻痹';
        if ($this->buff == 'poisoned') return '中毒';
        if ($this->buff == 'escape') return '逃跑';
        return '正常';
    }

    //取得此怪物的種族
    public function monster()
    {
        return $this->hasOne('App\Models\GameMonster', 'id', 'monster_id');
    }

    //取得此怪物可使用技能
    public function skills()
    {
        return $this->monster->skills->reject(function ($skill) {
            return $skill->pivot->level > $this->level;
        });
    }

    public function attack()
    {
        $critical = false;
        $hit = $this->crit_rate;
        $rnd = mt_rand()/mt_getrandmax();
        if ($hit >= 1 || $rnd < $hit) {
            $critical = true;
        }
        $skill = $this->skills()->random();
        $result = $skill->monster_cast($this->id, $this->uuid, $critical);
        return [ 'skill' => $skill, 'result' => $result ];
    }

}
