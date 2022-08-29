<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;
use Illuminate\Support\Facades\DB;

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
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    public function gmails()
	{
    	return $this->morphMany('App\Models\Gsuite', 'owner');
	}

    public function tutor_classroom()
	{
    	return $this->belongsTo('App\Models\Classroom', 'tutor_class');
	}

    public function units()
	{
    	return $this->belongsToMany('App\Models\Unit', 'job_title', 'uuid', 'unit_id');
	}

    public function union()
    {
        $uni = [];
        $units = $this->units;
        foreach ($units as $u) {
            $uni[] = $u;
            if (!$u->is_main()) {
                $uni[] = $u->parent();
            }
        }
        return $uni;
    }

    public function roles()
	{
    	return $this->belongsToMany('App\Models\Role', 'job_title', 'uuid', 'role_id');
	}


    public function assignment()
	{
        $assignment = DB::table('assignment')->where('uuid', $this->uuid)->get();
    	return $assignment;
	}

    public function subjects()
	{
    	return $this->belongsToMany('App\Models\Subject', 'assignment', 'uuid', 'subject_id');
	}
    
    public function classrooms()
	{
    	return $this->hasMany('App\Models\Classroom', 'assignment', 'uuid', 'class_id');
	}

    public function sync()
    {
        $sso = new SSO();
        $sso->fetch_user($this->uuid);
        $this->fresh();
    }

    public function expired()
	{
        $expire = new Carbon($this->updated_at);
    	return Carbon::today() > $expire->addDays(config('services.tpedu.expired_days'));
	}
}
