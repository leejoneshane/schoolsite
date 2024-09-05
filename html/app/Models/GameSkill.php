<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSkill extends Model
{

    protected $table = 'game_skills';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',        //技能名稱
        'gif_file',    //特效圖檔
        'object',      //作用對象：self 自己，party 隊伍，partner 指定的我方角色，target 指定的敵方角色，all 敵方隊伍中的所有角色
        'hit_rate',    //命中率，擊中判斷為 命中率＋（自己敏捷力-對方敏捷力）/100
        'cost_mp',     //消耗行動力
        'level',       //可使用此技能之等級 
        'effect_hp',   //對作用對象的健康值增減效益，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'effect_mp',   //對作用對象的行動力增減效益
        'effect_ap',   //對作用對象的攻擊力增減效益
        'effect_dp',   //對作用對象的防禦力增減效益
        'effect_sp',   //對作用對象的敏捷力增減效益
        'effect_times',//技能持續時間，以分鐘為單位
        'earn_xp',     //施展技能後，可獲得經驗值
        'earn_gp',     //施展技能後，可獲得金幣
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'professions',
    ];

    //取得此技能包含於哪些職業
    public function professions()
    {
        return $this->belongsToMany('App\Models\GameClass', 'game_classes_skills', 'skill_id', 'class_id')->withPivot(['level']);
    }

    //施展指定的技能，指定對象為 Array|String ，傳回結果陣列，0 => 成功，5 => 失敗
    public function cast($self, $uuids)
    {
        if (is_string($uuids)) $uuids[] = $uuids;
        $me = GameCharacter::find($self);
        foreach ($uuids as $uuid) {
            $character = GameCharacter::find($uuid);
            $result[$uuid] = $this->effect($me, $character);
        }
        $me->mp -= $this->cost_mp;
        $me->xp += $this->earn_xp;
        $me->gp += $this->earn_gp;
        $me->save();
        return $result;
    }

    //套用技能效果
    public function effect($me, $character)
    {
        $hit = $this->hit_rate;
        if ($this->object == 'target' || $this->object == 'all') {
            $hit += ($me->sp - $character->sp) / 100;
        }
        if ($hit >= 1 || rand() < $hit) {
            if ($this->effect_hp != 0) {
                if ($this->effect_hp > 1 || $this->effect_hp < -1) {
                    $character->hp += $this->effect_hp;
                } else {
                    $character->hp += intval($character->max_hp * $this->effect_hp);
                }
            }
            if ($this->effect_mp != 0) {
                if ($this->effect_mp > 1 || $this->effect_mp < -1) {
                    $character->mp += $this->effect_mp;
                } else {
                    $character->mp += intval($character->max_mp * $this->effect_mp);
                }
            }
            if ($this->effect_ap != 0) {
                $character->temp_effect = 'ap';
                $character->effect_value = $this->effect_ap;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->effect_dp != 0) {
                $character->temp_effect = 'dp';
                $character->effect_value = $this->effect_dp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->effect_sp != 0) {
                $character->temp_effect = 'sp';
                $character->effect_value = $this->effect_sp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            $character->save();
            return 0;
        } else {
            return MISS;
        }
    }

}
