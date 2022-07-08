<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpedussoServiceProvider as SSO;

class Unit extends Model
{

	protected $table = 'units';
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
        'name',
    ];

    public function roles()
    {
        return $this->hasMany('App\Models\Role', 'unit_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'jobs', 'unit_id', 'uuid');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

}
