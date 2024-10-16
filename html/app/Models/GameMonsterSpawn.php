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
        'monster_id',  //怪物種類
        'name',        //怪物名稱
        'level',       //怪物等級
        'url',         //圖片網址
        'max_hp',      //最大健康值
        'hp',          //目前健康值
        'hit_rate',    //攻擊命中率
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
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'monster',
        'skills',
    ];

    //更新角色時，自動刪除
    protected static function booted()
    {
        static::updated(function($item)
        {
            if ($item->hp == 0) {
                $item->delete();
            }
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
                $this->tmp_effect = null;
                $this->effect_value = null;
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
                $this->tmp_effect = null;
                $this->effect_value = null;
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
                $this->tmp_effect = null;
                $this->effect_value = null;
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

    //取得此怪物的種族
    public function monster()
    {
        return $this->hasOne('App\Models\GameMonster', 'id', 'monster_id');
    }

    //取得此怪物可使用技能
    public function skills()
    {
        return $this->monster->skills->reject(function ($skill) {
            return $skill->level > $this->level;
        });
    }

    public function attack($uuid)
    {
        $character = GameCharacter::find($uuid);
        $damage = 0;
        $hit = $this->crit_rate;
        $rnd = mt_rand()/mt_getrandmax();
        if ($hit >= 1 || $rnd < $hit) {
            $skill = $this->skills->random();
            return $skill->monster_cast($this->id, $uuid);
        } else {
            $hit = $this->hit_rate;
            $hit += ($this->final_sp - $character->final_sp) / 100;
            $rnd = mt_rand()/mt_getrandmax();
            $message = $me->name.'對'.$character->name.'進行一般攻擊';
            if ($hit >= 1 || $rnd < $hit) {
                $damage = $this->final_ap * 2 - $character->final_dp;
                if ($character->buff == 'reflex') {
                    if ($damage > 0 && $this->hp > 0) {
                        $this->hp -= $damage;
                        $this->save();
                        broadcast(new GameCharacterChannel($character->stdno, $message.'，但是傷害被反射回自己！'));    
                    }
                } elseif ($character->buff != 'invincible') {
                    if ($damage > 0 && $character->hp > 0) {
                        $character->hp -= $damage;
                        $character->save();
                        broadcast(new GameCharacterChannel($character->stdno, $message.'，但是沒有受傷！'));
                    }
                } else {
                    broadcast(new GameCharacterChannel($character->stdno, $message.'成功！'));
                }                
            } else {
                broadcast(new GameCharacterChannel($character->stdno, $message.'失敗！'));
                return MISS;
            }
        }
    }

}
