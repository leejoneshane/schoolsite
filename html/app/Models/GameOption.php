<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameOption extends Model
{

    protected $table = 'game_options';

    //以下屬性可以批次寫入
    protected $fillable = [
        'evaluate_id', //評量代號
        'question_id', //題目代號
        'option',      //選項內容
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'evaluate',
        'question',
    ];

    //取得此選項所屬試卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此選項所屬題目
    public function question()
    {
        return $this->hasOne('App\Models\GameQuestion', 'id', 'question_id');
    }

    //檢查指定選項是否為正確答案
    public function is_correct()
    {
        return $this->question->is_corrent($this->id);
    }

}
