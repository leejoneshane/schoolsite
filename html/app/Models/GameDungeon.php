<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameDungeon extends Model
{

    protected $table = 'game_dungeons';

    //以下屬性可以批次寫入
    protected $fillable = [
        'title',        //地下城名稱
        'description',  //地下城闖關說明
        'classroom_id', //施測班級
        'evaluate_id',  //評量代號
        'monster_id',   //配置的怪物
        'opened_at',    //開放日期
        'closed_at',    //關閉日期
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'evaluate',
        'monster',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'is_open',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'opened_at' => 'datetime:Y-m-d',
        'closed_at' => 'datetime:Y-m-d',
    ];

    //提供開放狀態
    public function getIsOpenAttribute()
    {
        return Carbon::now() >= $this->opened_at && Carbon::now() < $this->closed_at;
    }

    //取得此地下城的測驗卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此地下城的班級
    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'id', 'classroom_id');
    }

    //取得此地下城的怪物
    public function monster()
    {
        return $this->hasOne('App\Models\GameMonster', 'id', 'monster_id');
    }

}
