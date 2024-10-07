<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\GamePartyChannel;
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
        'status',    //解除角色狀態
        'gp',        //此道具的購買價格
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'passive' => 'boolean',
    ];

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

    //使用指定的道具，指定對象為 Array|String ，傳回結果陣列，0 => 成功，5 => 失敗
    public function cast($owner, $uuid = null, $party_id = null)
    {
        $self = GameCharacter::find($owner);
        if ($party_id) {
            $party = GameParty::find($party_id);
            $message = $self->name.'對'.$party->name.'使用道具'.$this->name;
            broadcast(new GamePartyChannel($self->party_id, $message.'！'));
            if ($self->party_id != $party_id) {
                broadcast(new GamePartyChannel($party_id, $message.'！'));
            }
            foreach ($party->members as $m) {
                $result[$m->uuid] = $this->effect($m);
                if ($result[$m->uuid] == MISS) {
                    broadcast(new GamePartyChannel($self->party_id, $m->name.'未命中！'));
                    if ($self->party_id != $party_id) {
                        broadcast(new GamePartyChannel($party_id, $m->name.'未命中！'));
                    }
                } else {
                    broadcast(new GamePartyChannel($self->party_id, $m->name.'命中！'));
                    if ($self->party_id != $party_id) {
                        broadcast(new GamePartyChannel($party_id, $m->name.'命中！'));
                    }
                }
            }
        } elseif ($uuid) {
            $character = GameCharacter::find($uuid);
            if ($character->party_id) {
                $result[$uuid] = $this->effect($character);
                $message = $self->name.'對'.$character->name.'使用道具'.$this->name;
                if ($result[$uuid] == MISS) {
                    broadcast(new GamePartyChannel($self->party_id, $message.'失敗！'));
                } else {
                    broadcast(new GamePartyChannel($self->party_id, $message.'成功！'));
                }
                if ($self->party_id != $character->party_id) {
                    if ($result[$uuid] == MISS) {
                        broadcast(new GamePartyChannel($character->party_id, $message.'失敗！'));
                    } else {
                        broadcast(new GamePartyChannel($character->party_id, $message.'成功！'));
                    }
                }
            }
        } else {
            $result[$owner] = $this->effect($self);
            $message = $self->name.'對自己使用道具'.$this->name;
            if ($self->party_id) {
                if ($result[$owner] == MISS) {
                    broadcast(new GamePartyChannel($self->party_id, $message.'失敗！'));
                } else {
                    broadcast(new GamePartyChannel($self->party_id, $message.'成功！'));
                }
            }
        }
        return $result;
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
                $character->effect_timeout = Carbon::now()->addMinutes(40);
            }
            if ($this->dp != 0) {
                $character->temp_effect = 'dp';
                $character->effect_value = $this->dp;
                $character->effect_timeout = Carbon::now()->addMinutes(40);
            }
            if ($this->sp != 0) {
                $character->temp_effect = 'sp';
                $character->effect_value = $this->sp;
                $character->effect_timeout = Carbon::now()->addMinutes(40);
            }
            $character->save();
            return 0;
        } else {
            return MISS;
        }
    }

}
