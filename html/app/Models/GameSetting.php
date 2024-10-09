<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GameCharacter;
use App\Events\GamePartyChannel;

class GameSetting extends Model
{

    protected $table = 'game_settings';

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

    //進行獎勵
    public static function positive_act($teacher, $uuids, $rule_id = null, $reason = null, $xp = null, $gp = null, $item_id = null)
    {
        $add = [];
        if ($rule_id) {
            $rule = GameSetting::find($rule_id);
            $message = '因為'.$rule->description.'獲得上天的祝福：';
        } else {
            $message = '因為'.$reason.'獲得上天的祝福：';
        }
        if ($xp) {
            $add[] = '經驗值' . $xp . '點';
        }
        if ($gp) {
            $add[] = '金幣' . $gp . '枚';
        }
        if ($item_id) {
            $item = GameItem::find($item_id);
            $add[] = '道具' . $item->name . '一個';
        }
        $message .= implode('、', $add).'。';
        if (is_string($uuids)) $uuids[] = $uuids;
        foreach ($uuids as $uuid) {
            $character = GameCharacter::find($uuid);
            if ($xp) {
                $character->xp += $xp;
            }
            if ($gp) {
                $character->gp += $gp;
            }
            if ($item_id) {
                $character->get_item($item_id);
            }
            $character->save();
            GameLog::create([
                'classroom_id' => session('gameclass'),
                'uuid' => $teacher,
                'character_uuid' => $character->uuid,
                'content' => $character->seat.' '.$character->name.$message,
            ]);
            if ($character->party_id) {
                broadcast(new GamePartyChannel($character->party_id, $character->seat.' '.$character->name.$message));
            }
        }
    }

    //進行懲罰
    public static function negative_act($teacher, $uuids, $rule_id = null, $reason = null, $hp = null, $mp = null)
    {
        if (is_string($uuids)) $uuids[] = $uuids;
        foreach ($uuids as $uuid) {
            $character = GameCharacter::find($uuid);
            $message = $character->seat.' '.$character->name;
            $add = [];
            if ($rule_id) {
                $rule = GameSetting::find($rule_id);
                $message .= '因為'.$rule->description.'受到天罰損失：';
            } else {
                $message .= '因為'.$reason.'受到天罰損失：';
            }
            if ($hp) {
                $add[] = '生命力' . $hp . '點';
            }
            if ($mp) {
                $add[] = '法力（行動力）' . $mp . '點';
            }
            $message .= implode('、', $add).'。';
            if ($character->status == "DEAD") {
                $message .= '但因為冒險者已經死亡，所以沒有作用';
                GameLog::create([
                    'classroom_id' => session('gameclass'),
                    'uuid' => $teacher,
                    'character_uuid' => $character->uuid,
                    'content' => $message,
                ]);
                break;
            }
            $hatred = $protect = false;
            foreach ($character->teammate() as $m) {
                if ($character->buff == 'hatred') {
                    $hatred = $character;
                    $character->buff = null;
                    $character->save();
                    break;
                }
                if ($character->buff == 'protect') {
                    $protect = $character;
                    $character->buff = null;
                    $character->save();
                }
            }
            $add = [];
            if ($hatred) {
                $target = $hatred;
                $message .= $hatred->seat.' '.$hatred->name.'挺身而出，承受傷害！';
            } else {
                $target = $character;
            }
            if ($target->buff == 'invincible') {
                $target->buff = null;
                $target->save();
                $message .= '因為處於無敵狀態，沒有受傷！';
            } elseif ($target->buff == 'reflex') {
                $target->buff = null;
                $target->save();
                $message .= '但是因為處於傷害反射狀態，沒有受傷！';
            } elseif ($target->buff == 'protected') {
                $target->buff = null;
                $target->save();
                $message .= $protect->seat.' '.$protect->name.'挺身而出，承受傷害！';
                if ($hp && $hp > 0) {
                    $protect->hp -= $hp;
                }
                if ($mp && $mp > 0) {
                    $protect->mp -= $mp;
                }
                $protect->save();
            } elseif ($target->buff == 'apportion') {
                $message .= '但是因為同伴分擔傷害，所有隊員受傷：';
                $count = $target->teammate()->count();
                if ($hp && $hp > 0) {
                    $damage = intval($hp / $count);
                    $add[] = '生命力'.$damage.'點';
                    foreach ($target->teammate() as $c) {
                        $c->hp -= $damage;
                        $c->save();
                    }
                }
                if ($mp && $mp > 0) {
                    $damage = intval($mp / $count);
                    $add[] = '法力（行動力）'.$damage.'點';
                    foreach ($target->teammate() as $c) {
                        $c->mp -= $damage;
                        $c->save();
                    }
                }
                $message .= implode('、', $add).'。';
            } else {
                if ($hp && $hp > 0) {
                    $target->hp -= $hp;
                }
                if ($mp && $mp > 0) {
                    $target->mp -= $mp;
                }
                $target->save();
            }
            GameLog::create([
                'classroom_id' => session('gameclass'),
                'uuid' => $teacher,
                'character_uuid' => $target->uuid,
                'content' => $message,
            ]);
            if ($target->party_id) {
                broadcast(new GamePartyChannel($target->party_id, $message));
            }
        }
    }

}
