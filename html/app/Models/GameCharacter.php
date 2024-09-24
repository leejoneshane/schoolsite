<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\GameSence;
use App\Models\Classroom;

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
        'classroom_id',   //班級代號
        'party_id',   //公會編號
        'seat',       //學生座號
        'title',      //角色稱號
        'name',       //角色姓名
        'class_id',   //職業代號
        'image_id',   //圖片編號
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
        'absent',        //缺席
        'pick_up',       //中籤次數
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
        'items',
        'image',
    ];

    //更新角色時，自動進行升級
    protected static function booted()
    {
        self::updating(function ($item) {
            if ($item->hp < 1) $item->hp = 0;
            if ($item->hp > $item->max_hp) $item->hp = $item->max_hp;
            if ($item->mp < 1) $item->mp = 0;
            if ($item->mp > $item->max_mp) $item->mp = $item->max_mp;
        });

        static::updated(function($item)
        {
            if ($item->xp > 0) {
                $item->levelup();
            }
        });
    }

    //選取可抽籤的角色，靜態函式
    public static function wheel($room_id)
    {
        $data = GameCharacter::select(DB::raw('MIN(pick_up) AS min, MAX(pick_up) AS max'))
            ->where('classroom_id', $room_id)
            ->get()->first();
        if ($data->min == $data->max) {
            return GameCharacter::where('classroom_id', $room_id)->where('absent', 0)->get();
        } else {
            return GameCharacter::where('classroom_id', $room_id)->where('pick_up', $data->min)->where('absent', 0)->get();
        }
    }

    //選取可抽籤的角色，靜態函式
    public static function withoutAbsent($room_id)
    {
        return GameCharacter::where('classroom_id', $room_id)->where('absent', 0)->get();
    }

    //篩選指定的班級的所有角色
    public static function findByClass($classroom)
    {
        $uuids = Classroom::find($classroom)->uuids();
        $students = GameCharacter::whereIn('uuid', $uuids)->orderBy('seat')->get();
        return $students;
    }

    //篩選指定的公會的所有角色
    public static function findByParty($party)
    {
        return GameCharacter::where('party_id', $party)->get();
    }

    //篩選無公會角色
    public static function findNoParty($classroom)
    {
        $room = Classroom::find($classroom);
        return GameCharacter::whereIn('uuid', $room->uuids())->whereNull('party_id')->get();
    }

    //提供 AP 計算結果
    public function getFinalApAttribute()
    {
        $ap = $this->ap;
        if ($this->party) {
            if ($this->party->effect_ap != 0) {
                $i = intval($this->party->effect_ap);
                $d = $this->party->effect_ap - $i;
                if ($i != 0) {
                    $this->ap += $i;
                }
                if ($d != 0) {
                    $this->ap += intval($this->ap * $d);
                }
            }
        }
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
            $ap = intval($ap * 0.5);
        }
        return $ap;
    }

    //提供 DP 計算結果
    public function getFinalDpAttribute()
    {
        $dp = $this->dp;
        if ($this->party) {
            if ($this->party->effect_dp != 0) {
                $i = intval($this->party->effect_dp);
                $d = $this->party->effect_dp - $i;
                if ($i != 0) {
                    $this->dp += $i;
                }
                if ($d != 0) {
                    $this->dp += intval($this->dp * $d);
                }
            }
        }
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
            $dp = intval($dp * 0.5);
        }
        return $dp;
    }

    //提供 SP 計算結果
    public function getFinalSpAttribute()
    {
        $sp = $this->sp;
        if ($this->party) {
            if ($this->party->effect_sp != 0) {
                $i = intval($this->party->effect_sp);
                $d = $this->party->effect_sp - $i;
                if ($i != 0) {
                    $this->sp += $i;
                }
                if ($d != 0) {
                    $this->sp += intval($this->sp * $d);
                }
            }
        }
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
            $sp = intval($sp * 0.5);
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

    //檢查此角色是否可升級，若可以則進行升級
    public function levelup()
    {
        if ($this->profession) {
            while ($this->xp >= static::$levelup_needed[$this->level + 1]) {
                $this->level ++;
                if (rand() < $this->profession->hp_lvlup) {
                    if ($this->profession->hp_lvlup >= 1) {
                        $this->max_hp += rand(1,$this->profession->hp_lvlup);
                    } else {
                        $this->max_hp++;
                    }
                }
                if (rand() < $this->profession->mp_lvlup) {
                    if ($this->profession->mp_lvlup >= 1) {
                        $this->max_mp += rand(1, $this->profession->mp_lvlup);
                    } else {
                        $this->max_mp++;
                    }
                }
                if (rand() < $this->profession->ap_lvlup) {
                    if ($this->profession->ap_lvlup >= 1) {
                        $this->ap += rand(1, $this->profession->ap_lvlup);
                    } else {
                        $this->ap++;
                    }
                }
                if (rand() < $this->profession->dp_lvlup) {
                    if ($this->profession->dp_lvlup >= 1) {
                        $this->dp += rand(1, $this->profession->dp_lvlup);
                    } else {
                        $this->dp++;
                    }
                }
                if (rand() < $this->profession->sp_lvlup) {
                    if ($this->profession->sp_lvlup >= 1) {
                        $this->sp += rand(1, $this->profession->sp_lvlup);
                    } else {
                        $this->sp++;
                    }
                }
                $this->hp = $this->max_hp;
                $this->save();    
            }
        }
    }

    //檢查此角色是否可升級，若可以則進行升級
    public function force_levelup($level)
    {
        $this->xp = static::$levelup_needed[$level];
        if ($level == 1) {
            $this->max_hp = $this->profession->base_hp;
            $this->hp = $this->profession->base_hp;
            $this->max_mp = $this->profession->base_mp;
            $this->mp = $this->profession->base_mp;
            $this->ap = $this->profession->base_ap;
            $this->dp = $this->profession->base_dp;
            $this->sp = $this->profession->base_sp;    
        }
        $this->save();
        $this->levelup();
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
    public function image()
    {
        return $this->hasOne('App\Models\GameImage', 'id', 'image_id');
    }

    //取得此角色的隊伍物件
    public function party()
    {
        return $this->hasOne('App\Models\GameParty', 'id', 'party_id');
    }

    //取得此角色的已出席夥伴，不包含自己
    public function teammate()
    {
        return $this->belongsTo('App\Models\GameCharacter', 'party_id', 'party_id')
            ->where('uuid', '!=', $this->uuid)
            ->where('absent', 0);
    }

    //取得此角色的已出席夥伴
    public function members()
    {
        return $this->belongsTo('App\Models\GameCharacter', 'party_id', 'party_id')
            ->where('absent', 0);
    }

    //取得此角色擁有的道具
    public function items()
    {
        return $this->belongsToMany('App\Models\GameItem', 'game_characters_items', 'uuid', 'item_id')
            ->withPivot(['quantity']);
    }

    //取得此角色可使用道具
    public function useable_items()
    {
        return $this->items->reject(function ($item) {
            return $item->passive == 0;
        });
    }

    //取得此角色可使用技能
    public function skills()
    {
        return $this->profession->skills->reject(function ($skill) {
            return $skill->level > $this->level || $skill->cost_mp > $this->mp;
        });
    }

    //取得此角色可使用被動技能
    public function passive_skills()
    {
        return $this->profession->passive->reject(function ($skill) {
            return $skill->level > $this->level || $skill->cost_mp > $this->mp;
        });
    }

    //角色日常更新
    public function newday()
    {
        if (!$this->party) return;
        if ($this->party->configure) {
            $this->mp += $this->party->configure->daily_mp;
            if ($this->party->effect_hp != 0) {
                $i = intval($this->party->effect_hp);
                $d = $this->party->effect_hp - $i;
                if ($i != 0) {
                    $this->hp += $i;
                }
                if ($d != 0) {
                    $this->hp += intval($this->max_hp * $d);
                }
            }
            if ($this->party->effect_mp != 0) {
                $i = intval($this->party->effect_mp);
                $d = $this->party->effect_mp - $i;
                if ($i != 0) {
                    $this->mp += $i;
                }
                if ($d != 0) {
                    $this->mp += intval($this->max_mp * $d);
                }
            }
            if ($this->mp < 1) {
                $this->mp = 0;
                $this->status = 'COMA';
            }
            if ($this->mp > $this->max_mp) $this->mp = $this->max_mp;
            if ($this->hp < 1) {
                $this->hp = 0;
                $this->status = 'DEAD';
            }
            if ($this->hp > $this->max_hp) $this->hp = $this->max_hp;
            $this->absent = false;
            $this->save();
        }
    }

    //使用指定的技能
    public function use_skill($id, $uuid = null, $party_id = null, $item_id = null)
    {
        if ($this->status == 'DEAD') return DEAD;
        if ($this->status == 'COMA') return COMA;
        if ($this->buff == 'paralysis') {
            if ($this->effect_timeout >= Carbon::now()) {
                return COMA;
            } else {
                $this->effect_timeout = null;
                $this->buff = null;
                $this->save();
            }
        }
        if (!($this->skills()->contains('id', $id))) return NOT_EXISTS;
        $skill = GameSkill::find($id);
        $classroom = $this->student->class_id;
        if (!$skill->passive && !GameSence::is_lock($classroom)) return PEACE;
        $skill->cast($this->uuid, $uuid, $party_id, $item_id);
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
    public function use_item($id, $uuid = null, $party_id = null)
    {
        if ($this->status == 'DEAD') return DEAD;
        if ($this->buff == 'paralysis') {
            if ($this->effect_timeout >= Carbon::now()) {
                return COMA;
            } else {
                $this->effect_timeout = null;
                $this->buff = null;
                $this->save();
            }
        }
        if (!($this->items->contains('id', $id))) return NOT_EXISTS;
        $item = GameItem::find($id);
        $classroom = $this->student->class_id;
        if (!$item->passive && !GameSence::is_lock($classroom)) return PEACE;
        $item->cast($this->uuid, $uuid, $party_id);
        DB::table('game_characters_items')
            ->where('uuid', $this->uuid)
            ->where('item_id', $item->id)
            ->decrement('quantity');
        DB::table('game_characters_items')
            ->where('uuid', $this->uuid)
            ->where('item_id', $item->id)
            ->where('quantity', '<', 1)
            ->delete();
    }

}
