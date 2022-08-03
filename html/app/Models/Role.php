<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

class Role extends Model
{

	protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'role_no',
        'unit_id',
        'name',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'role_id', 'uuid');
    }

}
