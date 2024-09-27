<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GameParty;
use App\Models\GameCharacter;
use Carbon\Carbon;

class GameSkill extends Model
{

    protected $table = 'game_skills';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',        //技能名稱
        'description', //技能簡介
        'gif_file',    //特效圖檔
        'passive',     //是否為被動技能(非戰鬥用技能)
        'object',      //作用對象：self 自己，party 隊伍，partner 指定的我方角色，target 指定的敵方角色，all 敵方隊伍中的所有角色
        'hit_rate',    //命中率，擊中判斷為 命中率＋（自己敏捷力-對方敏捷力）/100
        'cost_mp',     //消耗行動力
        'ap',          //此技能的攻擊百分率，攻擊威力計算為 (ap + 自己攻擊力) - 對方防禦力 = 對方實際受傷點數
        'steal_hp',    //此技能的吸血百分率，計算方式為 對方實際受傷點數 * steal_hp = 我方補血點數
        'steal_mp',    //此技能的吸魔百分率，計算方式為 cost_mp * steal_mp = 我方補魔點數
        'steal_gp',    //此技能的偷盜百分率，計算方式為 對方的 gp * steal_gp = 對方失去的金幣(我方獲得金幣數)
        'effect_hp',   //對作用對象的健康值增減效益，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'effect_mp',   //對作用對象的行動力增減效益
        'effect_ap',   //對作用對象的攻擊力增減效益
        'effect_dp',   //對作用對象的防禦力增減效益
        'effect_sp',   //對作用對象的敏捷力增減效益
        'effect_times',//技能持續時間，以分鐘為單位
        'status',      //解除目標狀態，DEAD 死亡狀態復活，COMA 昏迷狀態回神
        'inspire',     //賦予目標狀態，invincible 無敵，reflex 反射傷害，protect 保護，protected 被保護，hatred 仇恨（集中傷害），apportion 分攤傷害，throw 投射道具, weak 虛弱
        'earn_xp',     //施展技能後，可獲得經驗值
        'earn_gp',     //施展技能後，可獲得金幣
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'professions',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'passive' => 'boolean',
    ];

    //取得此技能包含於哪些職業
    public function professions()
    {
        return $this->belongsToMany('App\Models\GameClass', 'game_classes_skills', 'skill_id', 'class_id')->withPivot(['level']);
    }

    //篩選指定職業的技能，靜態函式
    public static function forClass($class_id)
    {
        return GameSkill::select('game_skills.*', 'game_classes_skills.level')
            ->leftjoin('game_classes_skills', 'game_skills.id', '=', 'game_classes_skills.skill_id')
            ->where('game_classes_skills.class_id', $class_id)
            ->orderBy('game_classes_skills.level')
            ->get();
    }

    //篩選指定職業的技能，靜態函式
    public static function passive($class_id)
    {
        return GameSkill::select('game_skills.*', 'game_classes_skills.level')
            ->leftjoin('game_classes_skills', 'game_skills.id', '=', 'game_classes_skills.skill_id')
            ->where('game_classes_skills.class_id', $class_id)
            ->where('game_skills.passive', 1)
            ->orderBy('game_classes_skills.level')
            ->get();
    }

    public function image_path()
    {
        return public_path(GAME_SKILL.$this->gif_file);
    }

    public function image_url()
    {
        return asset(GAME_SKILL.$this->gif_file);
    }

    public function image_base64()
    {
        return base64_encode(file_get_contents($this->image_path()));
    }

    public function image_avaliable()
    {
        return $this->gif_file && file_exists($this->image_path());
    }

    //施展指定的技能，指定對象為 Array|String ，傳回結果陣列，0 => 成功，5 => 失敗
    public function cast($self, $uuid = null, $party_id = null, $item_id = null)
    {
        $result = [];
        $hatred = $protect = false;
        $me = GameCharacter::find($self);
        if ($this->inspire == "throw") {
            $item = GameItem::find($item_id);
            $item->cast($self, $uuid, $party_id);
        } else {
            if ($party_id) {
                $party = GameParty::find($party_id);
                $message = $me->name.'對'.$party->name.'施展技能'.$this->name;
                broadcast(new GamePartyChannel($me->party_id, $message.'！'));
                if ($me->party_id != $party_id) {
                    broadcast(new GamePartyChannel($party_id, $message.'！'));
                }
                if ($this->object == 'all') {
                    $targets = null;
                    foreach ($party->members as $m) {
                        $targets[] = $m;
                        if ($m->buff == 'hatred') {
                            $hatred = $m;
                            $m->buff = null;
                            $m->save();
                            break;
                        }
                        if ($m->buff == 'protect') {
                            $protect = $m;
                            $m->buff = null;
                            $m->save();
                        }
                    }
                    if ($hatred) $targets = [ $hatred ];
                    foreach ($targets as $t) {
                        if ($t->buff == 'invincible') {
                            $t->buff = null;
                            $t->save();
                            $result[$t->uuid] = MISS;
                        } elseif ($t->buff == 'protected') {
                            $t->buff = null;
                            $t->save();
                            $result[$t->uuid] = $this->effect_enemy($me, $protect);
                        } else {
                            $result[$t->uuid] = $this->effect_enemy($me, $t);
                        }
                        if ($result[$t->uuid] == MISS) {
                            broadcast(new GamePartyChannel($me->party_id, $t->name.'未命中！'));
                            broadcast(new GamePartyChannel($party_id, $t->name.'未命中！'));
                        } else {
                            broadcast(new GamePartyChannel($me->party_id, $t->name.'命中！'));
                            broadcast(new GamePartyChannel($party_id, $t->name.'命中！'));
                        }
                    }
                } elseif ($this->object == 'party') {
                    foreach ($party->members as $m) {
                        $result[$m->uuid] = $this->effect_friend($me, $m);
                        if ($result[$m->uuid] == MISS) {
                            broadcast(new GamePartyChannel($me->party_id, $m->name.'未命中！'));
                        } else {
                            broadcast(new GamePartyChannel($me->party_id, $m->name.'命中！'));
                        }
                    }
                }
            } elseif ($uuid) {
                $target = GameCharacter::find($uuid);
                if ($target->party_id) {
                    if ($this->object == 'target') {
                        $result[$uuid] = $this->effect_enemy($me, $target);
                    }
                    if ($this->object == 'partner') { 
                        $result[$uuid] = $this->effect_friend($me, $target);
                    }
                    $message = $me->name.'對'.$target->name.'施展技能'.$this->name;
                    if ($result[$uuid] == MISS) {
                        broadcast(new GamePartyChannel($me->party_id, $message.'失敗！'));
                    } else {
                        broadcast(new GamePartyChannel($me->party_id, $message.'成功！'));
                    }
                    if ($me->party_id != $target->party_id) {
                        if ($result[$uuid] == MISS) {
                            broadcast(new GamePartyChannel($target->party_id, $message.'失敗！'));
                        } else {
                            broadcast(new GamePartyChannel($target->party_id, $message.'成功！'));
                        }
                    }
                }
            } else {
                $result[$self] = $this->effect_friend($me);
                $message = $me->name.'對自己施展技能'.$this->name;
                if ($result[$self] == MISS) {
                    broadcast(new GamePartyChannel($me->party_id, $message.'失敗！'));
                } else {
                    broadcast(new GamePartyChannel($me->party_id, $message.'成功！'));
                }
            }    
        }
        $me->mp -= $this->cost_mp;
        if ($this->steal_mp > 0) $me->mp += $this->cost_mp * $this->steal_mp;
        $me->xp += $this->earn_xp;
        $me->gp += $this->earn_gp;
        $me->save();
        return $result;
    }

    public function effect_friend($me, $character = null)
    {
        if (!$character) $character = $me;
        $hit = $this->hit_rate;
        $rnd = mt_rand()/mt_getrandmax();
        if ($hit >= 1 || $rnd < $hit) {
            if ($this->inspire) {
                if ($this->inspire == 'protect') {
                    $me->buff = 'protect';
                    $character->buff = 'protected';
                }
                if ($this->inspire != 'throw') {
                    $character->buff = $this->inspire;
                }
            }
            if ($this->effect_hp != 0) {
                if ($character->status == 'DEAD') {
                    if ($this->status == 'DEAD') {
                        if ($this->effect_hp > 1 || $this->effect_hp < -1) {
                            $character->hp += $this->effect_hp;
                        } else {
                            $character->hp += intval($character->max_hp * $this->effect_hp);
                        }
                    }
                } else {
                    if ($this->effect_hp > 1 || $this->effect_hp < -1) {
                        $character->hp += $this->effect_hp;
                    } else {
                        $character->hp += intval($character->max_hp * $this->effect_hp);
                    }
                }
            }
            if ($this->effect_mp != 0) {
                if ($character->status == 'COMA') {
                    if ($this->status == 'COMA') {
                        if ($this->effect_mp > 1 || $this->effect_mp < -1) {
                            $character->mp += $this->effect_mp;
                        } else {
                            $character->mp += intval($character->max_mp * $this->effect_mp);
                        }
                    }
                } else {
                    if ($this->effect_mp > 1 || $this->effect_mp < -1) {
                        $character->mp += $this->effect_mp;
                    } else {
                        $character->mp += intval($character->max_mp * $this->effect_mp);
                    }
                }
            }
            if ($this->effect_ap != 0 && $character->status != 'DEAD') {
                $character->temp_effect = 'ap';
                $character->effect_value = $this->effect_ap;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->effect_dp != 0 && $character->status != 'DEAD') {
                $character->temp_effect = 'dp';
                $character->effect_value = $this->effect_dp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->effect_sp != 0 && $character->status != 'DEAD') {
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

    //套用技能效果
    public function effect_enemy($me, $character)
    {
        $hit = $this->hit_rate;
        $hit += ($me->final_sp - $character->final_sp) / 100;
        $rnd = mt_rand()/mt_getrandmax();
        if ($hit >= 1 || $rnd < $hit) {
            $damage = 0;
            if ($this->ap > 0) {
                if ($character->buff == 'reflex') {
                    $damage = ($this->ap + $character->final_ap) - $me->final_dp;
                    if ($damage > 0) $me->hp -= $damage;
                } else {
                    $damage = $this->ap * 2 + ($me->final_ap - $character->final_dp);
                    if ($damage > 0) {
                        if ($character->buff == 'apportion') {
                            $count = $character->members->count();
                            $damage = intval($damage / $count);
                            foreach ($character->members as $c) {
                                if ($c->buff != 'reflex' && $c->buff != 'protected' && $c->buff != 'invincible') {
                                    $c->hp -= $damage / $count;
                                }
                            }
                        } else {
                            $character->hp -= $damage;  

                        }
                    }
                }
                if ($damage > 0 && $this->steal_hp > 0) {
                    $me->hp += $damage * $this->steal_hp;
                }
            }
            if ($this->effect_hp != 0) {
                if ($character->status == 'DEAD') {
                    if ($this->status == 'DEAD') {
                        if ($this->effect_hp > 1 || $this->effect_hp < -1) {
                            $character->hp += $this->effect_hp;
                        } else {
                            $character->hp += intval($character->max_hp * $this->effect_hp);
                        }
                    }
                } else {
                    if ($this->effect_hp > 1 || $this->effect_hp < -1) {
                        $character->hp += $this->effect_hp;
                    } else {
                        $character->hp += intval($character->max_hp * $this->effect_hp);
                    }
                }
            }
            if ($this->effect_mp != 0) {
                if ($character->status == 'COMA') {
                    if ($this->status == 'COMA') {
                        if ($this->effect_mp > 1 || $this->effect_mp < -1) {
                            $character->mp += $this->effect_mp;
                        } else {
                            $character->mp += intval($character->max_mp * $this->effect_mp);
                        }
                    }
                } else {
                    if ($this->effect_mp > 1 || $this->effect_mp < -1) {
                        $character->mp += $this->effect_mp;
                    } else {
                        $character->mp += intval($character->max_mp * $this->effect_mp);
                    }
                }
            }
            if ($this->effect_ap != 0 && $character->status != 'DEAD') {
                $character->temp_effect = 'ap';
                $character->effect_value = $this->effect_ap;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->effect_dp != 0 && $character->status != 'DEAD') {
                $character->temp_effect = 'dp';
                $character->effect_value = $this->effect_dp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->effect_sp != 0 && $character->status != 'DEAD') {
                $character->temp_effect = 'sp';
                $character->effect_value = $this->effect_sp;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            if ($this->steal_gp > 0) {
                $gold = intval($character->gp * $this->steal_gp);
                $character->gp -= $gold;
                $me->gp += $gold;
            }
            if ($this->inspire) {
                $character->buff = $this->inspire;
                $character->effect_timeout = Carbon::now()->addMinutes($this->effect_times);
            }
            $character->save();
            return $damage;
        } else {
            return MISS;
        }
    }

}
