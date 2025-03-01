<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameDungeon extends Model
{

    protected $table = 'game_dungeons';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'syear',        //學年
        'uuid',         //指派者
        'title',        //地下城名稱
        'description',  //地下城闖關說明
        'classroom_id', //施測班級
        'evaluate_id',  //評量代號
        'monster_id',   //配置的怪物
        'times',        //可挑戰次數
        'opened_at',    //開放日期
        'closed_at',    //關閉日期
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'teacher',
        'classroom',
        'evaluate',
        'monster',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'level',
        'is_open',
        'style',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'opened_at' => 'datetime:Y-m-d',
        'closed_at' => 'datetime:Y-m-d',
    ];

    //提供推薦等級
    public function getLevelAttribute()
    {
        return 'L'.$this->monster->max_level;
    }

    //提供開放狀態
    public function getIsOpenAttribute()
    {
        return Carbon::now() >= $this->opened_at && Carbon::now() < $this->closed_at;
    }

    //提供文字樣式
    public function getStyleAttribute()
    {
        return $this->monster->style;
    }

    //篩選指定教師指派的所有地下城
    public static function findByTeacher($uuid)
    {
        return GameDungeon::where('uuid', $uuid)
            ->where('syear', current_year())
            ->orderBy('classroom_id')
            ->get();
    }

    //篩選指定試卷的所有地下城
    public static function findByEvaluate($id)
    {
        return GameDungeon::where('evaluate_id', $id)
            ->where('syear', current_year())
            ->orderBy('classroom_id')
            ->get();
    }

    //篩選指定班級的所有地下城
    public static function findByClassroom($id)
    {
        return GameDungeon::where('classroom_id', $id)
            ->where('syear', current_year())
            ->orderBy('opened_at', 'desc')
            ->get();
    }

    //取得此地下城的指派者
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
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

    //取得此地下城的所有答案卷
    public function answers()
    {
        return GameAnswer::findBy($this->evaluate_id, $this->classroom_id);
    }

}
