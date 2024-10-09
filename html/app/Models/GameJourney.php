<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class GameJourney extends Model
{

    protected $table = 'game_journeys';

    //以下屬性可以批次寫入
    protected $fillable = [
        'evaluate_id', //評量代號
        'answer_id',   //答案卷代號
        'option_id',   //回答選項
        'is_correct',  //是否正確
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'evaluate',
        'answer',
        'option',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    //取得此歷程紀錄所屬試卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此歷程紀錄所屬答案卷
    public function answer()
    {
        return $this->hasOne('App\Models\GameAnswer', 'id', 'answer_id');
    }

    //取得此歷程紀錄回答選項
    public function option()
    {
        return $this->hasOne('App\Models\GameOption', 'id', 'option_id');
    }

}
