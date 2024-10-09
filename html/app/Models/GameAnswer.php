<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class GameAnswer extends Model
{

    protected $table = 'game_answers';

    //以下屬性可以批次寫入
    protected $fillable = [
        'evaluate_id', //評量代號
        'uuid',        //答題者
        'score',       //得分
        'tested_at',   //測驗時間
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'evaluate',
        'student',
        'journeys',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'student_name',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'tested_at' => 'datetime:Y-m-d',
    ];
    
    //提供此答案卷的答題者姓名
    public function getStudentNameAttribute()
    {
        return $this->student->realname;
    }

    //取得此答案卷所屬試卷
    public function evaluate()
    {
        return $this->hasOne('App\Models\GameEvaluate', 'id', 'evaluate_id');
    }

    //取得此答案卷的答題學生
    public function student()
    {
        return $this->hasOne('App\Models\Student', 'uuid', 'uuid');
    }

    //取得此答案卷的所有答題紀錄
    public function journeys()
    {
        return $this->hasMany('App\Models\GameJourney', 'answer_id', 'id');
    }

}
