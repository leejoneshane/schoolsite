<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

class Role extends Model
{

	protected $table = 'roles';
	protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'unit_id',
        'name',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id', 'unit_id');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'role_id', 'uuid');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

}
