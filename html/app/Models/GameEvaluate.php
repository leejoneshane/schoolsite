<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameEvaluate extends Model
{

    protected $table = 'game_evaluates';

    //以下屬性可以批次寫入
    protected $fillable = [
        'title',       //評量名稱
        'subject',     //科目名稱
        'range',       //評量範圍
        'grade_id',    //適用年級
        'uuid',        //出題者
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'grade',
        'teacher',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'teacher_name',
    ];

    //提供此評量的出題教師姓名
    public function getTeacherNameAttribute()
    {
        return $this->teacher->realname;
    }

    //取得此評量的出題教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此評量的適用年級
    public function grade()
    {
        return $this->hasOne('App\Models\Grade', 'id', 'grade_id');
    }

}
