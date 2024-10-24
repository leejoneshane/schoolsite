<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\GameSence;
use App\Models\Classroom;
use App\Models\GameClass;
use App\Models\GameDungeon;
use App\Models\GameAnswer;

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
        'stdno',
        'final_ap',
        'final_dp',
        'final_sp',
        'status',
        'status_desc',
        'url',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'configure',
        'student',
        'profession',
        'party',
        'teammate',
        'members',
        'items',
        'image',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'effect_timeout' => 'datetime:Y-m-d H:i:s',
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

    //提供學生名牌號碼（班級座號）
    public function getStdnoAttribute()
    {
        return $this->classroom_id . (($this->seat < 10) ? '0'.$this->seat : $this->seat);
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
                    $ap += $i;
                }
                if ($d != 0) {
                    $ap += intval($this->ap * $d);
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
        if ($this->party) {
            if ($this->party->effect_dp != 0) {
                $i = intval($this->party->effect_dp);
                $d = $this->party->effect_dp - $i;
                if ($i != 0) {
                    $dp += $i;
                }
                if ($d != 0) {
                    $dp += intval($this->dp * $d);
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
        if ($this->party) {
            if ($this->party->effect_sp != 0) {
                $i = intval($this->party->effect_sp);
                $d = $this->party->effect_sp - $i;
                if ($i != 0) {
                    $sp += $i;
                }
                if ($d != 0) {
                    $sp += intval($this->sp * $d);
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

    //提供角色狀態
    public function getStatusAttribute()
    {
        if ($this->hp < 1) return DEAD;
        if ($this->mp < 1) return COMA;
        return NORMAL;
    }

    //提供角色狀態
    public function getStatusDescAttribute()
    {
        if ($this->hp < 1) return '死亡';
        if ($this->mp < 1) return '昏迷';
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

    //提供角色圖片網址
    public function getUrlAttribute()
    {
        return $this->image ? $this->image->url() : null;
    }

    //檢查此角色是否可升級，若可以則進行升級
    public function levelup()
    {
        if ($this->profession) {
            while ($this->xp >= static::$levelup_needed[$this->level + 1]) {
                $this->level ++;
                $rnd = mt_rand()/mt_getrandmax();
                if ($rnd < $this->profession->hp_lvlup) {
                    if ($this->profession->hp_lvlup >= 1) {
                        $this->max_hp += rand(1,$this->profession->hp_lvlup);
                    } else {
                        $this->max_hp++;
                    }
                }
                $rnd = mt_rand()/mt_getrandmax();
                if ($rnd < $this->profession->mp_lvlup) {
                    if ($this->profession->mp_lvlup >= 1) {
                        $this->max_mp += rand(1, $this->profession->mp_lvlup);
                    } else {
                        $this->max_mp++;
                    }
                }
                $rnd = mt_rand()/mt_getrandmax();
                if ($rnd < $this->profession->ap_lvlup) {
                    if ($this->profession->ap_lvlup >= 1) {
                        $this->ap += rand(1, $this->profession->ap_lvlup);
                    } else {
                        $this->ap++;
                    }
                }
                $rnd = mt_rand()/mt_getrandmax();
                if ($rnd < $this->profession->dp_lvlup) {
                    if ($this->profession->dp_lvlup >= 1) {
                        $this->dp += rand(1, $this->profession->dp_lvlup);
                    } else {
                        $this->dp++;
                    }
                }
                $rnd = mt_rand()/mt_getrandmax();
                if ($rnd < $this->profession->sp_lvlup) {
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

    //變更職業
    public function change_class($id)
    {
        $new = GameClass::find($id);
        if ($this->level == 1) {
            $this->max_hp = $new->base_hp;
            $this->hp = $new->base_hp;
            $this->max_mp = $new->base_mp;
            $this->mp = $new->base_mp;
            $this->ap = $new->base_ap;
            $this->dp = $new->base_dp;
            $this->sp = $new->base_sp;
        }
        $this->class_id = $id;
        $this->save();
    }

    //取得此班級組態
    public function configure()
    {
        return $this->hasOne('App\Models\GameConfigure', 'classroom_id', 'classroom_id');
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
        return GameCharacter::where('party_id', $this->party_id)
            ->where('uuid', '!=', $this->uuid)
            ->where('absent', 0)->get();
    }

    //取得此角色的已出席夥伴
    public function members()
    {
        return GameCharacter::where('party_id', $this->party_id)
            ->where('absent', 0)->get();
    }

    //取得此角色擁有的道具
    public function items()
    {
        return $this->belongsToMany('App\Models\GameItem', 'game_characters_items', 'uuid', 'item_id')
            ->withPivot(['quantity']);
    }

    //取得此角色指定對象的所有道具
    public function items_by_object($object)
    {
        return $this->items->filter(function ($item) use ($object) {
            if ($object == 'self') {
                return $item->object == 'self' || $item->object == 'partner';
            } else {
                return $item->object == $object;
            }
        });
    }

    //取得此角色可投擲道具
    public function throw_items()
    {
        return $this->items->reject(function ($item) {
            return $item->passive == 1;
        });
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
            return $skill->pivot->level > $this->level || $skill->cost_mp > $this->mp;
        });
    }

    //取得此角色指定對象可使用技能
    public function skills_by_object($object)
    {
        return $this->profession->skills->reject(function ($skill) use ($object) {
            if ($skill->pivot->level > $this->level) return true;
            if ($skill->cost_mp > $this->mp) return true;
            if ($skill->object == 'any') return false;
            if ($object == 'self') {
                if ($skill->object == 'self' || $skill->object == 'partner') return false;
            } else {
                if ($skill->object != $object) return true;
            }
            return false;
        });
    }

    //取得此角色可使用戰鬥技能
    public function fight_skills()
    {
        return $this->profession->fight->reject(function ($skill) {
            return $skill->pivot->level > $this->level || $skill->cost_mp > $this->mp;
        });
    }

    //取得此角色可使用被動技能
    public function passive_skills()
    {
        return $this->profession->passive->reject(function ($skill) {
            return $skill->pivot->level > $this->level || $skill->cost_mp > $this->mp;
        });
    }

    //檢查此角色是否為公會長
    public function is_leader()
    {
        if ($this->party) {
            return $this->uuid == $this->party->uuid;
        }
        return false;
    }

    //計算此角色進入指定地下城的次數
    public function dungeon_times($dungeon_id)
    {
        return GameAnswer::findByUuid($dungeon_id, $this->uuid)->count();
    }

    //取得此角色可進入的地下城
    public function dungeons()
    {
        $dungeons = GameDungeon::findByClassroom($this->classroom_id);
        $dungeons->reject(function ($dun) {
            if ($dun->opened_at > Carbon::today() || $dun->closed_at < Carbon::today()) return true;
            if ($dun->times == 0) return false;
            $times = $this->dungeon_times($dun->id);
            if ($dun->times <= $times) return true;
            return false;
        });
        return $dungeons;
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
            }
            if ($this->mp > $this->max_mp) $this->mp = $this->max_mp;
            if ($this->hp < 1) {
                $this->hp = 0;
            }
            if ($this->hp > $this->max_hp) $this->hp = $this->max_hp;
            $this->absent = false;
            $this->save();
        }
    }

    //使用指定的技能
    public function use_skill($id, $uuid = null, $party_id = null, $item_id = null)
    {
        if ($this->status == 'DEAD') return 'dead';
        if ($this->status == 'COMA') return 'coma';
        if ($this->buff == 'paralysis') {
            if ($this->effect_timeout >= Carbon::now()) {
                return 'coma';
            } else {
                $this->effect_timeout = null;
                $this->buff = null;
                $this->save();
            }
        }
        if (!($this->skills()->contains('id', $id))) return 'not_exists';
        $skill = GameSkill::find($id);
        if ($uuid || $this->party_id) {
            $result = $skill->cast($this->uuid, $uuid, $party_id, $item_id);
            return $result;
        }
    }

    //使用指定的技能在怪物身上
    public function use_skill_on_monster($id, $monster_id, $item_id = null)
    {
        if ($this->status == 'DEAD') return 'dead';
        if ($this->status == 'COMA') return 'coma';
        if ($this->buff == 'paralysis') {
            if ($this->effect_timeout >= Carbon::now()) {
                return 'coma';
            } else {
                $this->effect_timeout = null;
                $this->buff = null;
                $this->save();
            }
        }
        if (!($this->skills()->contains('id', $id))) return 'not_exists';
        $skill = GameSkill::find($id);
        $result = $skill->cast_on_monster($this->uuid, $monster_id, $item_id);
        return $result;
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

    //賣掉指定的道具
    public function sell_item($id)
    {
        $item = $this->items->firstWhere('id', $id);
        if (!$item) return NOT_EXISTS;
        if ($item->pivot->quantity > 1) {
            DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->decrement('quantity');
        } else {
            DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->delete();
        }
        $this->gp += $item->gp;
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

    ///失去指定的道具
    public function loss_item($id)
    {
        $item = GameItem::find($id);
        $record = $this->items->firstWhere('id', $id); 
        if ($record) {
            if ($record->pivot->quantity > 1) {
                DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->decrement('quantity');
            } else {
                DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->delete();
            }
        }
    }

    //使用指定的道具
    public function use_item($id, $uuid = null, $party_id = null)
    {
        if ($this->status == 'DEAD') return 'dead';
        if ($this->buff == 'paralysis') {
            if ($this->effect_timeout >= Carbon::now()) {
                return 'coma';
            } else {
                $this->effect_timeout = null;
                $this->buff = null;
                $this->save();
            }
        }
        if (!($this->items->contains('id', $id))) return 'not_exists';
        $item = GameItem::find($id);
        if ($uuid || $this->party_id) {
            $result = $item->cast($this->uuid, $uuid, $party_id);
            DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->decrement('quantity');
            DB::table('game_characters_items')
                ->where('uuid', $this->uuid)
                ->where('item_id', $item->id)
                ->where('quantity', '<', 1)
                ->delete();
            return $result;
        }
    }

    //使用指定的道具
    public function use_item_on_monster($id, $monster_id)
    {
        if ($this->status == 'DEAD') return 'dead';
        if ($this->buff == 'paralysis') {
            if ($this->effect_timeout >= Carbon::now()) {
                return 'coma';
            } else {
                $this->effect_timeout = null;
                $this->buff = null;
                $this->save();
            }
        }
        if (!($this->items->contains('id', $id))) return 'not_exists';
        $item = GameItem::find($id);
        $result = $item->effect_monster($monster_id);
        DB::table('game_characters_items')
            ->where('uuid', $this->uuid)
            ->where('item_id', $item->id)
            ->decrement('quantity');
        DB::table('game_characters_items')
            ->where('uuid', $this->uuid)
            ->where('item_id', $item->id)
            ->where('quantity', '<', 1)
            ->delete();
        return $result;
    }

}
