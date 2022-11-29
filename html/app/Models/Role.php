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
        'organize',
    ];

    public static function findByName($name)
    {
        return Role::where('name', 'like', '%'.$name.'%')->first();
    }

    public static function filter($role_no)
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('role_no', $role_no)
            ->orderBy('top')
            ->get();
    }

    public static function director()
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('roles.organize', true)
            ->where('roles.role_no', 'C02')
            ->orderBy('top')
            ->get();
    }

    public static function organize()
    {
        return Role::selectRaw('roles.*, LEFT(units.unit_no, 3) as top')
            ->join('units', 'units.id', '=', 'roles.unit_id')
            ->where('roles.organize', true)
            ->where('roles.role_no', '!=', 'C02')
            ->orderBy('top')
            ->get();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'role_id', 'uuid')->where('year', current_year());
    }

}
