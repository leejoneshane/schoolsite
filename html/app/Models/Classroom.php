<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{

    protected $table = 'classrooms';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'grade_id',
        'tutor',
        'name',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'grade',
        'tutor',
        'students',
        'teachers',
        'subjects',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'tutor' => 'array',
    ];

    //取得此班級的年級
    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }

    //取得此班級導師（因為允許複數導師，所以傳回集合）
    public function tutors()
    {
        return $this->hasMany('App\Models\Teacher', 'tutor_class');
    }

    //取得此班級的就讀學生，依座號排序
    public function students()
    {
        return $this->hasMany('App\Models\Student', 'class_id')->orderBy('seat');
    }

    //取得此班級的任教老師，依真實姓名排序
    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'assigment', 'class_id', 'uuid')->orderBy('realname');
    }

    //取得此班級的配課科目
    public function subjects()
    {
        return $this->belongsToMany('App\Models\Subject', 'assigment', 'class_id', 'subject_id');
    }

}
