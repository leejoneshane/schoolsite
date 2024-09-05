<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameParty extends Model
{

    protected $table = 'game_parties';

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id',
        'uuid',        //建立教師
        'name',        //公會名稱
        'group_no',    //第幾組
        'description', //公會口號
        'base_id',     //公會基地的編號
        'effect_hp',   //據點對隊伍成員在健康上面的增益，2 則加 2 點，0.5 則加 50%
        'effect_mp',   //據點對隊伍成員在行動力上面的增益
        'effect_ap',   //據點對隊伍成員在攻擊力上面的增益
        'effect_dp',   //據點對隊伍成員在防禦力上面的增益
        'effect_sp',   //據點對隊伍成員在敏捷力上面的增益
        'treasury',    //據點金庫
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'teacher',
        'members',
        'furnitures',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'furnitures' => 'array',
    ];

    //篩選指定的班級和教師
    public static function findBy($classroom, $uuid)
    {
        return GameParty::where('classroom_id', $classroom)->where('uuid', $uuid)->get();
    }

    //取得此隊伍的所屬班級
    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom');
    }

    //取得此隊伍的指導教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此隊伍的所有角色
    public function members()
    {
        return $this->hasMany('App\Models\GameCharacter', 'party_id', 'id');
    }

    //取得此隊伍的基地
    public function foundation()
    {
        return $this->belongsTo('App\Models\GameBase');
    }

    //取得此隊伍的所有家具
    public function furnitures()
    {
        return $this->belongsToMany('App\Models\GameFurniture', 'game_parties_furnitures', 'party_id', 'furniture_id');
    }

    //購買指定的家具
    public function buy_furniture($id)
    {
        $furniture = $this->furnitures->firstWhere('id', $id); 
        if ($furniture) return ALREADY_EXISTS;
        if ($this->treasury < $furniture->gp) return NOT_ENOUGH_GP;
        DB::table('game_parties_furnitures')->insert([
            'party_id' => $this->id,
            'furniture_id' => $furniture->id,
        ]);
        $this->treasury -= $furniture->gp;
        $this->save();
    }

    //移除指定的家具
    public function remove_furniture($id)
    {
        $furniture = $this->furnitures->firstWhere('id', $id); 
        if ($furniture) {
            DB::table('game_parties_furnitures')->where('party_id', $this->id)->where('furniture_id', $id)->delete();
        }
    }

}
