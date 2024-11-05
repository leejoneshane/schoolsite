<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameItem extends Model
{

    protected $table = 'game_items';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',      //道具名稱
        'description',  //道具簡介
        'image_file',
        'passive',   //是否為被動道具(非戰鬥用道具)
        'object',    //作用對象：self 自己，party 隊伍，partner 指定的我方角色，target 指定的敵方角色，all 敵方隊伍中的所有角色
        'hit_rate',  //命中率，擊中判斷為 命中率-(對方敏捷力/100)
        'hp',        //對作用對象的健康值增減效益，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'mp',        //對作用對象的行動力增減效益
        'ap',        //對作用對象的攻擊力增減效益
        'dp',        //對作用對象的防禦力增減效益
        'sp',        //對作用對象的敏捷力增減效益
        'effect_times',//技能持續時間，以分鐘為單位
        'status',      //解除目標狀態，DEAD 死亡狀態復活，COMA 昏迷狀態回神
        'inspire',     //賦予目標狀態，invincible 無敵，reflex 反射傷害，protect 保護，protected 被保護，hatred 仇恨（集中傷害），apportion 分攤傷害，throw 投射道具, weak 虛弱, paralysis 麻痹, poisoned 中毒
        'gp',        //此道具的購買價格
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'passive' => 'boolean',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'status_str',
        'inspire_str',
    ];

    //提供此道具解除狀態中文說明
    public function getStatusStrAttribute()
    {
        if ($this->status == 'DEAD') return '死亡';
        if ($this->status == 'COMA') return '昏迷';
        return '';
    }

    //提供此道具賦予狀態中文說明
    public function getInspireStrAttribute()
    {
        if ($this->inspire == 'invincible') return '無敵狀態';
        if ($this->inspire == 'hatred') return '集中仇恨';
        if ($this->inspire == 'protect') return '護衛';
        if ($this->inspire == 'protected') return '被保護';
        if ($this->inspire == 'reflex') return '傷害反射';
        if ($this->inspire == 'apportion') return '分散傷害';
        if ($this->inspire == 'weak') return '身體虛弱';
        if ($this->inspire == 'paralysis') return '精神麻痹';
        if ($this->inspire == 'poisoned') return '中毒';
        if ($this->inspire == 'escape') return '逃跑';
        return '';
    }

    //篩選指定職業的技能，靜態函式
    public static function passive()
    {
        return GameSkill::where('passive', 1)->get();
    }

    public function image_path()
    {
        return public_path(GAME_ITEM.$this->image_file);
    }

    public function image_url()
    {
        return asset(GAME_ITEM.$this->image_file);
    }

    public function image_base64()
    {
        return base64_encode(file_get_contents($this->image_path()));
    }

    public function image_avaliable()
    {
        return $this->image_file && file_exists($this->image_path());
    }

    //使用指定的道具，傳回結果陣列，0 => 成功，5 => 失敗
    public function cast($owner, $uuid = null, $party_id = null)
    {
        $self = GameCharacter::find($owner);
        if ($party_id) {
            $party = GameParty::find($party_id);
            foreach ($party->members as $m) {
                $result[$m->seat] = $this->effect($m);
            }
        } elseif ($uuid) {
            $character = GameCharacter::find($uuid);
            if ($character->party_id) {
                $result[$character->seat] = $this->effect($character);
            }
        } else {
            $result[$self->seat] = $this->effect($self);
        }
        return $result;
    }

    //套用道具效果
    public function effect_monster($monster_id)
    {
        $monster = GameMonster::find($monster_id);
        $hit = $this->hit_rate;
        if ($this->object == 'target' || $this->object == 'all') {
            $hit -= $monster->final_sp / 100;
        }
        $rnd = mt_rand()/mt_getrandmax();
        if ($hit >= 1 || $rnd < $hit) {
            if ($this->hp != 0) {
                if ($this->hp > 1 || $this->hp < -1) {
                    $monster->hp += $this->hp;
                } else {
                    $monster->hp += intval($monster->max_hp * $this->hp);
                }
            }
            if ($this->ap != 0) {
                $monster->temp_effect = 'ap';
                $monster->effect_value = $this->ap;
                $monster->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->dp != 0) {
                $monster->temp_effect = 'dp';
                $monster->effect_value = $this->dp;
                $monster->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->sp != 0) {
                $monster->temp_effect = 'sp';
                $monster->effect_value = $this->sp;
                $monster->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->inspire) {
                $monster->buff = $this->inspire;
                $monster->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($monster->hp < 1) {
                $monster->character->xp += $monster->xp;
                $monster->character->gp += $monster->gp;
                $monster->character->save();
            }
            $monster->save();
            return 0;
        } else {
            return 'miss';
        }
    }

    //套用道具效果
    public function effect($character)
    {
        $hit = $this->hit_rate;
        if ($this->object == 'target' || $this->object == 'all') {
            $hit -= $character->final_sp / 100;
        }
        $rnd = mt_rand()/mt_getrandmax();
        if ($hit >= 1 || $rnd < $hit) {
            if ($character->buff == 'invincible') {
                $character->buff = null;
                $character->save();
                return 'miss';
            }
            if ($this->hp != 0) {
                if ($character->status == 'DEAD') {
                    if ($this->status == 'DEAD') {
                        if ($this->hp > 1 || $this->hp < -1) {
                            $character->hp += $this->hp;
                        } else {
                            $character->hp += intval($character->max_hp * $this->hp);
                        }
                    }
                } else {
                    if ($this->hp > 1 || $this->hp < -1) {
                        $character->hp += $this->hp;
                    } else {
                        $character->hp += intval($character->max_hp * $this->hp);
                    }
                }
            }
            if ($this->mp != 0) {
                if ($character->status == 'COMA') {
                    if ($this->status == 'COMA') {
                        if ($this->mp > 1 || $this->mp < -1) {
                            $character->mp += $this->mp;
                        } else {
                            $character->mp += intval($character->max_hp * $this->mp);
                        }
                    }
                } else {
                    if ($this->mp > 1 || $this->mp < -1) {
                        $character->mp += $this->mp;
                    } else {
                        $character->mp += intval($character->max_hp * $this->mp);
                    }
                }
            }
            if ($this->ap != 0) {
                $character->temp_effect = 'ap';
                $character->effect_value = $this->ap;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->dp != 0) {
                $character->temp_effect = 'dp';
                $character->effect_value = $this->dp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->sp != 0) {
                $character->temp_effect = 'sp';
                $character->effect_value = $this->sp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->inspire) {
                $character->buff = $this->inspire;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            $character->save();
            return 0;
        } else {
            return 'miss';
        }
    }

}
