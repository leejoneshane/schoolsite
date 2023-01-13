<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{

    protected $table = 'grades';
    public $incrementing = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'name',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classrooms',
    ];

    //取得此年級所有的班級
    public function classrooms()
    {
        return $this->hasMany('App\Models\Classroom');
    }

}
