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
        return $this->hasMany('App\Models\Role', 'dept_id', 'id');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

}
