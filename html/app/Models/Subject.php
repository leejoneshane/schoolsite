<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{

    protected $table = 'subjects';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'name',
    ];

    //取得該科目所有任教教師
    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'assigment', 'subject_id', 'uuid');
    }

    //取得該科目有配課的班級
    public function classrooms()
    {
        return $this->belongsToMany('App\Models\Classroom', 'assigment', 'subject_id', 'class_id');
    }

}
