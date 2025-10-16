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

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'name',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'theme',
        'creater',
        'students',
    ];

    //提供座位表名稱
    public function getNameAttribute()
    {
        return $this->classroom->name . $this->theme->name . '座位表';
    }

    //篩選指定使用者、指定班級建立的所有座位表，傳回集合物件
    public static function findBy($uuid, $cls_id)
    {
        return Seats::where('uuid', $uuid)->where('class_id', $cls_id)->first();
    }

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
            ->withPivot([
                'sequence',
                'group_no',
            ])
            ->orderByPivot('group_no')
            ->orderByPivot('sequence');
    }

    //取得不在此座位表上的所有學生
    public function students_without()
    {
        return $this->classroom->students->diff($this->students);
    }

    //取得此座位表的表徵陣列
    public function matrix()
    {
        $order = [];
        $whole = $this->theme->matrix;
        foreach ($whole as $row => $columns) {
            foreach ($columns as $col => $group) {
                if ($group > 0) {
                    if (isset($order[$group])) {
                        $order[$group]++;
                    } else {
                        $order[$group] = 1;
                    }
                    $stu = $this->students()
                        ->wherePivot('sequence', $order[$group])
                        ->wherePivot('group_no', $group)
                        ->first();
                    $whole[$row][$col] = (object) [
                        'student' => $stu,
                        'sequence' => $order[$group],
                        'group' => $group,
                    ];
                } else {
                    $whole[$row][$col] = (object) [
                        'student' => null,
                        'sequence' => 0,
                        'group' => 0,
                    ];
                }
            }
        }
        return $whole;
    }

}
