<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_no',
        'unit_id',
        'name',
    ];

    public static function current_year()
    {
        if (date('m') > 7) {
            $year = date('Y') - 1911;
        } else {
            $year = date('Y') - 1912;
        }
        return $year;
    }

    public static function filter($role_no)
    {
        //return Role::where('role_no', $role_no)->get();
        return Role::select('roles.*', 'LEFT(`units.unit_no`, 3) as top')->join('units', 'units.id', '=', 'roles.unit_id')->where('role_no', $role_no)->orderBy('top')->get();
    }

    public static function findByName($name)
    {
        return Role::where('name', 'like', '%'.$name.'%')->first();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'role_id', 'uuid')->where('year', Role::current_year());
    }

}
