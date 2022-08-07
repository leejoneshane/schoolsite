<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

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

    public static function main()
    {
        return Unit::whereRaw('CHAR_LENGTH(id) = 3 or LEFT(id, 1) = ?', ['Z'])->get();
    }

    public static function sub($main)
    {
        if (strlen($main) > 3) return false;
        return Unit::whereRaw('LEFT(id, 3) = ?', [$main])->get();
    }

    public static function subkeys($main)
    {
        if (strlen($main) > 3) return array($main);
        $keys = [];
        $subs = Unit::sub($main);
        if ($subs) {
            foreach ($subs as $sub) {
                $keys[] = $sub->id;
            }    
        }
        return $keys;
    }
    
    public function roles()
    {
        return $this->hasMany('App\Models\Role');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'job_title', 'unit_id', 'uuid');
    }

}
