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
        'rdate',
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
        if (!empty($this->rdate)) {
            return $this->rdate;
        } else {
            $datestrs = [];
            foreach ($this->datetimes as $d) {
                $datestrs[] = $d['date'] . ' ' . $d['from'] . '-' . $d['to']; 
            }
            return implode('、', $datestrs);
        }
    }

    //取得此公假單的建立者
    public function creater()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此公假單的所有學生
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'dayoff_students', 'dayoff_id', 'uuid')
            ->withPivot(['id'])
            ->orderBy('class_id');
    }

    //取得此公假單上指定班級的所有學生
    public function class_students($class)
    {
        return $this->students()->where('class_id', $class)->get();
    }

    //計算此公假單上的所有學生人數
    public function count_students()
    {
        return $this->students()->count();
    }

    //檢查指定 UUID 是否為公假單建立者
    public function is_creater($uuid)
    {
        return $this->uuid == $uuid;
    }

    //檢查指定 UUID 是否已經在公假單中
    public function student_occupy($uuid)
    {
        return DB::table('dayoff_students')->where('dayoff_id', $this->id)->where('uuid', $uuid)->exists();
    }

}
