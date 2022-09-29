<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;
use Carbon\Carbon;
use App\Models\Grade;

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
    ];
    
    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    public function gmails()
	{
        return $this->morphMany('App\Models\Gsuite', 'owner');
	}
    
    public function grade()
    {
        return Grade::find(substr($this->class_id, 0, 1));
    }

    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'class_id');
    }

    public function enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll', 'uuid', 'uuid');
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
