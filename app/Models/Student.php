<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;
use Carbon\Carbon;

class Student extends Model
{

    use SoftDeletes;
    
	protected $table = 'students';
	protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'idno',
        'account',
        'sn',
        'gn',
        'realname',
        'class_id',
        'seat',
        'birthdate',
        'gender',
        'email',
        'mobile',
        'telephone',
        'address',
        'www',
        'character',
        'is_deleted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    public function gmails()
	{
    	return $this->hasMany('App\Models\Gsuite', 'uuid', 'uuid');
	}
    
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'id', 'class_id');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

    public function expired()
	{
        $expire = new Carbon(self::$updated_at);
    	return Carbon::today() > $expire->addDays(config('app.expired_days'));
	}

}
