<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dayoff extends Model
{

	protected $table = 'dayoff';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'reason',
        'datetimes',
        'location',
        'who',
        'memo',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'datetimes' => 'array',
        'who' => 'boolean',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'datetime',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'creater',
        'students',
    ];

    //提供公假時間字串
    public function getDatetimeAttribute()
    {
        $datestrs = [];
        foreach ($this->datetimes as $d) {
            $datestrs[] = $d->date . '　' . $d->from . '～' . $d->to; 
        }
        return implode('、', $datestrs);
    }

    //取得此公假單的建立者
    public function creater()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此座位表上的所有學生
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', ' dayoff_students', 'dayoff_id', 'uuid')->orderBy('class_id');
    }

    //取得不在此座位表上的所有學生
    public function class_students($class)
    {
        return $this->students()->where('class_id', $class)->get();
    }

}
