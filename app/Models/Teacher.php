<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;

class Teacher extends Model
{

    use SoftDeletes;
    
	protected $table = 'teachers';
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
        'unit_id',
        'unit_name',
        'role_id',
        'role_name',
        'tutor_class',
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

    public function __get($name) //testing
    {
        if ($this->expired()) $this->sync();
        return parent::__get();
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    public function gmails()
	{
    	return $this->hasMany('App\Models\Gsuite', 'uuid', 'uuid');
	}
    
    public function units()
	{
    	return $this->hasMany('App\Models\Unit', 'jobs', 'uuid', 'unit_id');
	}

    public function roles()
	{
    	return $this->hasMany('App\Models\Role', 'jobs', 'uuid', 'role_id');
	}

    public function subjects()
	{
    	return $this->hasMany('App\Models\Subject', 'assignment', 'uuid', 'subject_id');
	}
    
    public function classrooms()
	{
    	return $this->hasMany('App\Models\Classroom', 'assignment', 'uuid', 'class_id');
	}

    public function sync()
    {
        $sso = new SSO();
        $sso->fetch_user(self::$uuid);
        $this->fresh();
    }

    public function expired()
	{
        $expire = new Carbon(self::$updated_at);
    	return Carbon::today() > $expire->addDays(config('app.expired_days'));
	}
}
