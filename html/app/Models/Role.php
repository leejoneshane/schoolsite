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

    public static function findByNo($role_no)
    {
        return Role::where('role_no', $role_no)->first();
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
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'role_id', 'uuid');
    }

}
