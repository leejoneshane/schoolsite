<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\GameBase;

class GameParty extends Model
{

    protected $table = 'game_parties';

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id',
        'group_no',    //第幾組
        'name',        //公會名稱
        'description', //公會口號
        'uuid',      //組長
        'base_id',     //公會基地的編號
        'effect_hp',   //據點對隊伍成員在健康上面的增益，2 則加 2 點，0.5 則加 50%
        'effect_mp',   //據點對隊伍成員在行動力上面的增益
        'effect_ap',   //據點對隊伍成員在攻擊力上面的增益
        'effect_dp',   //據點對隊伍成員在防禦力上面的增益
        'effect_sp',   //據點對隊伍成員在敏捷力上面的增益
        'treasury',    //據點金庫
        'pick_up',     //中籤次數
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'members',
        'foundation',
        'furnitures',
        'leader',
    ];

    //選取可抽籤的公會
    public static function wheel($room_id)
    {
        $data = GameParty::select(DB::raw('MIN(pick_up) AS min, MAX(pick_up) AS max'))
            ->where('classroom_id', $room_id)
            ->get()->first();
        if ($data->min == $data->max) {
            return GameParty::findByClass($room_id);
        } else {
            return GameParty::where('classroom_id', $room_id)->where('pick_up', $data->min)->get();
        }
    }

    //篩選指定的班級
    public static function findByClass($classroom)
    {
        return GameParty::where('classroom_id', $classroom)->get();
    }

    //篩選指定的班級
    public static function findByGroup($classroom, $group)
    {
        return GameParty::where('classroom_id', $classroom)->where('group_no', $group)->first();
    }

    //取得此隊伍的所屬班級
    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom');
    }

    //取得此隊伍不含缺席的角色
    public function members()
    {
        return $this->hasMany('App\Models\GameCharacter', 'party_id', 'id')->where('absent', 0)->orderBy('seat');
    }

    //取得此隊伍的組長
    public function leader()
    {
        return $this->hasOne('App\Models\GameCharacter', 'uuid', 'uuid');
    }

    //取得此隊伍的所有角色（包含缺席）
    public function withAbsent()
    {
        return $this->hasMany('App\Models\GameCharacter', 'party_id', 'id');
    }

    //取得此隊伍的基地
    public function foundation()
    {
        return $this->hasOne('App\Models\GameBase', 'id', 'base_id');
    }

    //取得此隊伍的所有家具
    public function furnitures()
    {
        return $this->belongsToMany('App\Models\GameFurniture', 'game_parties_furnitures', 'party_id', 'furniture_id');
    }

    //移除據點
    public function remove_foundation()
    {
        $old = $this->foundation;
        if ($old) {
            $this->effect_hp -= $old->hp;
            $this->effect_mp -= $old->mp;
            $this->effect_ap -= $old->ap;
            $this->effect_dp -= $old->dp;
            $this->effect_sp -= $old->sp;
        }
        $this->base_id = null;
        $this->save();
    }

    //變更據點
    public function change_foundation($id)
    {
        $old = $this->foundation;
        $new = GameBase::find($id);
        if ($old) {
            $this->effect_hp -= $old->hp;
            $this->effect_mp -= $old->mp;
            $this->effect_ap -= $old->ap;
            $this->effect_dp -= $old->dp;
            $this->effect_sp -= $old->sp;
        }
        if ($new) {
            $this->effect_hp += $new->hp;
            $this->effect_mp += $new->mp;
            $this->effect_ap += $new->ap;
            $this->effect_dp += $new->dp;
            $this->effect_sp += $new->sp;
        }
        $this->base_id = $id;
        $this->save();
    }

    //購買指定的家具 老師榮譽的同學還有老師的同學集合，另外早上3456年級陳的同學請
    public function buy_furniture($id)
    {
        if ($this->furnitures) {
            $furniture = $this->furnitures->firstWhere('id', $id); 
            if ($furniture) return "already exists";
            if ($this->furnitures->count() > 4) return "fo 老師榮譽的同學還有老師的同學集合undation fulled";
        }
        $furniture = GameFurniture::find($id); 
        if ($this->treasury < $furniture->gp) return "not enough gp";
        DB::table('game_parties_furnitures')->insert([
            'party_id' => $this->id,
            'furniture_id' => $furniture->id,
        ]);
        $this->treasury -= $furniture->gp;
        $this->effect_hp += $furniture->hp;
        $this->effect_mp += $furniture->mp;
        $this->effect_ap += $furniture->ap;
        $this->effect_dp += $furniture->dp;
        $this->effect_sp += $furniture->sp;
        $this->save();
    }

    //移除指定的家具
    public function sell_furniture($id, $cash)
    {
        if ($this->furnitures) {
            $furniture = $this->furnitures->firstWhere('id', $id); 
            if ($furniture) {
                DB::table('game_parties_furnitures')->where('party_id', $this->id)->where('furniture_id', $id)->delete();
                $this->treasury += $cash;
                $this->effect_hp -= $furniture->hp;
                $this->effect_mp -= $furniture->mp;
                $this->effect_ap -= $furniture->ap;
                $this->effect_dp -= $furniture->dp;
                $this->effect_sp -= $furniture->sp;
                $this->save();
            }
        }
    }

    //加入指定的成員
    public function add_member($uuid)
    {
        $newgay = GameCharacter::find($uuid);
        if ($newgay) {
            $newgay->party_id = $this->id;
            $newgay->save();
        }
    }

    //移除指定的成員
    public function remove_member($uuid)
    {
        $gay = GameCharacter::find($uuid);
        if ($gay) {
            $gay->party_id = null;
            $gay->save();
        }
    }

}
