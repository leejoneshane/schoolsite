<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

class Teacher extends Model
{

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
        return $this->belongsTo('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    public function units()
	{
    	return $this->belongsToMany('App\Models\Unit', 'jobs', 'uuid', 'unit_id');
	}

    public function roles()
	{
    	return $this->belongsToMany('App\Models\Role', 'jobs', 'uuid', 'role_id');
	}

    public function subjects()
	{
    	return $this->belongsToMany('App\Models\Subject', 'assignment', 'uuid', 'subject_id');
	}
    
    public function classrooms()
	{
    	return $this->belongsToMany('App\Models\Classroom', 'assignment', 'uuid', 'class_id');
	}

    public function sync()
    {
        $sso = new SSO();
        $sso->fetch_user(self::$uuid);
        self::fresh();
    }

    public function expired()
	{
    	return Carbon::today() > new Carbon(self::updated_at)->addDays(config('app.expired_days'));
	}
}
