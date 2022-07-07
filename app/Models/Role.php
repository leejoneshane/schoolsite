<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpedussoServiceProvider as SSO;

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
        'dept_id',
        'name',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id', 'dept_id');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

}
