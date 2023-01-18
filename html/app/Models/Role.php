<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'roles';

    //以下屬性可以批次寫入
    protected $fillable = [
        'role_no',
        'unit_id',
        'name',
        'organize',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'unit',
        'teachers',
    ];

    //篩選指定名稱的職務，靜態函式
    public static function findByName($name)
    {
        return Role::where('name', 'like', '%'.$name.'%')->first();
    }

    //篩選指定代碼的職務，靜態函式
    public static function filter($role_no)
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('role_no', $role_no)
            ->orderBy('top')
            ->get();
    }

    //篩選所有行政人員職務，靜態函式
    public static function administrator()
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('roles.organize', true)
            ->orWhere(function ($query) {
                $query->where('roles.role_no', '!=', 'C05');
                $query->where('roles.role_no', '!=', 'C06');
            })
            ->orderBy('top')
            ->get();
    }

    //篩選職級為主任的職務，靜態函式
    public static function director()
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('roles.organize', true)
            ->where('roles.role_no', 'C02')
            ->orderBy('top')
            ->get();
    }

    //篩選所有的非主任行政人員（包含：組長、特殊任務），靜態函式
    public static function manager()
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('roles.organize', true)
            ->where('roles.role_no', '!=', 'C02')
            ->orderBy('top')
            ->get();
    }

    //篩選所有的級科任職務，靜態函式
    public static function general()
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('roles.role_no', 'C05')
            ->orWhere('roles.role_no', 'C06')
            ->orderBy('top')
            ->get();
    }

    //取得此職務配屬單位
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    //取得擔任此職務的所有教師
    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'role_id', 'uuid')->where('year', current_year());
    }

}
