<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\GameSence;

class GameCharacter extends Model
{

    protected $table = 'game_characters';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static $levelup_needed = [
        1 => 0,  //等級 => 經驗值
        2 => 100,
        3 => 300,
        4 => 600,
        5 => 1000,
        6 => 1500,
        7 => 2100,
        8 => 2800,
        9 => 3600,
        10 => 4500,
        11 => 5500,
        12 => 6600,
        13 => 7800,
        14 => 9100,
        15 => 10500,
        16 => 12000,
        17 => 13600,
        18 => 15300,
        19 => 17100,
        20 => 19000,
        21 => 21000,
        22 => 23500,
        23 => 26500,
        24 => 30000,
        25 => 34000,
        26 => 38500,
        27 => 43500,
        28 => 49000,
        29 => 55000,
        30 => 62000,
    ];

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',       //學生uuid
        'title',      //角色稱號
        'image_id',   //圖片編號
        'party_id',   //公會編號
        'class_id',   //職業代號
        'level',      //目前等級
        'xp',         //累計經驗值
        'max_hp',     //最大健康值
        'hp',         //目前健康值
        'max_mp',     //最大行動力
        'mp',         //目前行動力
        'ap',         //目前攻擊力
        'dp',         //目前防禦力
        'sp',         //目前敏捷力
        'gp',         //擁有的金幣
        'temp_effect',   //暫時增益:ap,mp,sp 其中之一
        'effect_value',  //增益值，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'effect_timeout',//增益結束時間，timestamp
        'buff',          //特殊效果
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
        'student',
        'profession',
        'party',
        'teammate',
        'configure',
        'items',
    ];

    //提供 AP 計算結果
    public function getFinalApAttribute()
    {
        $ap = $this->ap;
        if ($this->party->effect_ap != 0) {
            if ($this->party->effect_ap > 1 || $this->party->effect_ap < -1) {
                $ap += $this->party->effect_ap;
            } else {
                $ap += intval($this->ap * $this->party->effect_ap);
            }
        }
        if ($this->temp_effect == 'ap') {
            if (Carbon::now() < $this->effect_timeout) {
                if ($this->effect_value > 1 || $this->effect_value < -1) {
                    $ap += $this->effect_value;
                } else {
                    $ap += intval($this->ap * $this->effect_value);
                }
            }
        }
        return $ap;
    }

    //提供 DP 計算結果
    public function getFinalDpAttribute()
    {
        $dp = $this->dp;
        if ($this->party->effect_dp != 0) {
            if ($this->party->effect_dp > 1 || $this->party->effect_dp < -1) {
                $dp += $this->party->effect_dp;
            } else {
                $dp += intval($this->dp * $this->party->effect_dp);
            }
        }
        if ($this->temp_effect == 'dp') {
            if (Carbon::now() < $this->effect_timeout) {
                if ($this->effect_value > 1 || $this->effect_value < -1) {
                    $dp += $this->effect_value;
                } else {
                    $dp += intval($this->dp * $this->effect_value);
                }
            }
        }
        return $dp;
    }

    //提供 SP 計算結果
    public function getFinalSpAttribute()
    {
        $sp = $this->sp;
        if ($this->party->effect_sp != 0) {
            if ($this->party->effect_sp > 1 || $this->party->effect_sp < -1) {
                $sp += $this->party->effect_sp;
            } else {
                $sp += intval($this->sp * $this->party->effect_sp);
            }
        }
        if ($this->temp_effect == 'sp') {
            if (Carbon::now() < $this->effect_timeout) {
                if ($this->effect_value > 1 || $this->effect_value < -1) {
                    $sp += $this->effect_value;
                } else {
                    $sp += intval($this->sp * $this->effect_value);
                }
            }
        }
        return $sp;
    }

    //提供角色狀態
    public function getStatusAttribute()
    {
        if ($this->hp < 1) return DEAD;
        if ($this->mp < 1) return COMA;
        return NORMAL;
    }

    //更新角色時，自動進行升級
    public static function boot()
    {
        parent::boot();
        static::updated(function($item)
        {
            if ($item->xp > 0) {
                $item->levelup();
            }
        });
    }

    //檢查此角色是否可升級，若可以則進行升級
    public function levelup()
    {
        while ($this->xp >= $this->levelup_needed[$this->level + 1]) {
            $this->level += 1;
            if (rand() < $this->prefession->hp_lvlup) {
                if ($this->prefession->hp_lvlup >= 1) {
                    $this->max_hp += $this->prefession->hp_lvlup;
                } else {
                    $this->max_hp++;
                }
            }
            if (rand() < $this->prefession->mp_lvlup) {
                if ($this->prefession->mp_lvlup >= 1) {
                    $this->max_mp += $this->prefession->mp_lvlup;
                } else {
                    $this->max_mp++;
                }
            }
            if (rand() < $this->prefession->ap_lvlup) {
                if ($this->prefession->ap_lvlup >= 1) {
                    $this->ap += $this->prefession->ap_lvlup;
                } else {
                    $this->ap++;
                }
            }
            if (rand() < $this->prefession->dp_lvlup) {
                if ($this->prefession->dp_lvlup >= 1) {
                    $this->dp += $this->prefession->dp_lvlup;
                } else {
                    $this->dp++;
                }
            }
            if (rand() < $this->prefession->sp_lvlup) {
                if ($this->prefession->sp_lvlup >= 1) {
                    $this->sp += $this->prefession->sp_lvlup;
                } else {
                    $this->sp++;
                }
            }
            $this->hp = $this->max_hp;
            $this->save();
        }
    }

    //取得此角色的學生物件
    public function student()
    {
        return $this->hasOne('App\Models\Student', 'uuid', 'uuid')->withDefault();
    }

    //取得此角色的職業物件
    public function profession()
    {
        return $this->hasOne('App\Models\GameClass', 'id', 'class_id');
    }

    //取得此角色的隊伍物件
    public function party()
    {
        return $this->belongsTo('App\Models\GameParty', 'id', 'party_id');
    }

    //取得此角色的夥伴
    public function teammate()
    {
        return $this->belongsTo('App\Models\GameCharacter', 'party_id', 'party_id');
    }

    //取得此角色可使用技能
    public function items()
    {
        return $this->belongsToMany('App\Models\GameItem', 'game_characters_items', 'uuid', 'item_id')->withPivot(['quantity']);
    }

    //取得此角色可使用技能
    public function skills()
    {
        return $this->profession->skills->reject(function ($skill) {
            return $skill->level > $this->level;
        });
    }

    //角色日常更新
    public function newday()
    {
        $this->mp += $this->party->configure->daily_mp;
        if ($this->hp < $this->max_hp && $this->party->effect_hp != 0) {
            if ($this->party->effect_hp > 1 || $this->party->effect_hp < -1) {
                $this->hp += $this->party->effect_hp;
            } else {
                $this->hp += intval($this->hp * $this->party->effect_hp);
            }
        }
        if ($this->mp < $this->max_mp && $this->party->effect_mp != 0) {
            if ($this->party->effect_mp > 1 || $this->party->effect_mp < -1) {
                $this->mp += $this->party->effect_mp;
            } else {
                $this->mp += intval($this->mp * $this->party->effect_mp);
            }
        }
        if ($this->hp < 1) $this->hp = 0;
        if ($this->hp > $this->max_hp) $this->hp = $this->max_hp;
        if ($this->mp < 1) $this->mp = 0;
        if ($this->mp > $this->max_mp) $this->mp = $this->max_mp;
        $this->save();
    }

    //使用指定的道具
    public function use_skill($id, $uuids = null)
    {
        if ($this->status == 'DEAD') return DEAD;
        if ($this->status == 'COMA') return COMA;
        if (!$this->skills->contains('id', $id)) return NOT_EXISTS;
        $skill = GameSkill::find($id);
        $classroom = $this->student->class_id;
        if ($skill->object != 'self' && !GameSence::is_lock($classroom)) return PEACE;
        if ($this->mp < $skill->cost_mp) return LESS_MP;
        if (is_null($uuids)) $uuids[] = $this->uuid;
        $skill->cast($this->uuid, $uuids);
    }

    //購買指定的道具
    public function buy_item($id)
    {
        $item = GameItem::find($id);
        if ($this->gp < $item->gp) return NOT_ENOUGH_GP;
        if ($this->items->firstWhere('id', $id)) {
            DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->increment('quantity');
        } else {
            DB::table('game_characters_items')->insert([
                'uuid' => $this->uuid,
                'item_id' => $item->id,
                'quantity' => 1,
            ]);
        }
        $this->gp -= $item->gp;
        $this->save();
    }

    //獲得指定的道具
    public function get_item($id)
    {
        $item = GameItem::find($id);
        if ($this->items->firstWhere('id', $id)) {
            DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->increment('quantity');
        } else {
            DB::table('game_characters_items')->insert([
                'uuid' => $this->uuid,
                'item_id' => $item->id,
                'quantity' => 1,
            ]);
        }
    }

    //使用指定的道具
    public function use_item($id, $uuids = null)
    {
        if ($this->status == 'DEAD') return DEAD;
        if (!$this->items->contains('id', $id)) return NOT_EXISTS;
        $item = GameItem::find($id);
        $classroom = $this->student->class_id;
        if ($item->object != 'self' && !GameSence::is_lock($classroom)) return PEACE;
        DB::table('game_characters_items')
            ->where('uuid', $this->uuid)
            ->where('item_id', $item->id)
            ->decrement('quantity');
        DB::table('game_characters_items')
            ->where('uuid', $this->uuid)
            ->where('item_id', $item->id)
            ->where('quantity', '<', 1)
            ->delete();
        if (is_null($uuids)) $uuids[] = $this->uuid;
        $item->cast($uuids);
    }

}
