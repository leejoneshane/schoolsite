<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class GameAnswer extends Model
{

    protected $table = 'game_answers';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'dungeon_id',   //地下城代號
        'evaluate_id',  //評量代號
        'classroom_id', //答題者班級代號
        'seat',         //答題者座號
        'student',      //答題者姓名
        'score',        //得分
        'tested_at',    //測驗時間
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'dungeon',
        'evaluate',
        'journeys',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'tested_at' => 'datetime:Y-m-d',
    ];

    //自動移除所有歷程
    protected static function booted()
    {
        self::deleting(function($item)
        {
            foreach ($item->journeys as $j) {
                $j->delete();
            }
        });
    }

    //篩選指定地下城的所有答案卷
    public static function findByDungeon($dungeon_id)
    {
        return GameAnswer::where('dungeon_id', $dungeon_id)
            ->orderBy('seat')
            ->get();
    }

    //篩選指定地下城指定學生的所有答案卷
    public static function findByUuid($dungeon_id, $uuid)
    {
        return GameAnswer::where('dungeon_id', $dungeon_id)
            ->where('uuid', $uuid)
            ->orderBy('created_at')
            ->get();
    }

    //取得此答案卷所屬地下城
    public function dungeon()
    {
        return $this->hasOne('App\Models\GameDungeon', 'id', 'dungeon_id');
    }

    //取得此答案卷所屬試卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此答案卷的所有答題紀錄
    public function journeys()
    {
        return $this->hasMany('App\Models\GameJourney', 'answer_id', 'id');
    }

}
