<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Seats extends Model
{

	protected $table = 'seats';

    //以下屬性可以批次寫入
    protected $fillable = [
        'class_id',
        'theme_id',
        'uuid',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'theme',
        'creater',
        'students',
    ];

    //篩選指定使用者建立的所有座位表，傳回集合物件
    public static function findByUUID($uuid)
    {
        return Seats::where('uuid', $uuid)->get();
    }

    //篩選指定班級的所有座位表，傳回集合物件
    public static function findByClass($cls_id)
    {
        return Seats::where('class_id', $cls_id)->get();
    }

    //篩選指定版型的所有座位表，傳回集合物件
    public static function findByTheme($theme_id)
    {
        return Seats::where('theme_id', $theme_id)->get();
    }

    //取得此座位表所屬班級
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'class_id');
    }

    //取得此座位表所套用的版型
    public function theme()
    {
        return $this->belongsTo('App\Models\SeatsTheme', 'theme_id');
    }

    //取得此座位表的建立者
    public function creater()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此座位表上的所有學生
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'seats_students', 'seats_id', 'uuid')
            ->as('seat')
            ->withPivot([
                'sequence',
                'group_no',
            ]);
    }

    //取得此座位表的表徵陣列
    public function matrix()
    {
        $order = [];
        $stus = $this->students;
        $whole = $this->theme->matrix;
        foreach ($whole as $row => $columns) {
            foreach ($columns as $col => $group) {
                $order[$group] += 1;
                $stu = $stus->where('sequence', $order[$group])->where('group_no', $group)->first();    
                $whole[$row][$col] = $stu;
            }
        }
        return $whole;
    }

}
