<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameQuestion extends Model
{

    protected $table = 'game_questions';

    //以下屬性可以批次寫入
    protected $fillable = [
        'evaluate_id', //評量代號
        'sequence',    //題目順序
        'question',    //題幹
        'answer',      //正確選項代號
        'score',       //配分
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'evaluate',
        'correct',
    ];

    //取得此題目所屬試卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此題目所屬試卷
    public function correct()
    {
        return GameOption::find($this->answer);
    }

    //檢查指定選項是否為正確答案
    public function is_correct($option_id)
    {
        return $this->answer == $option_id;
    }

}
