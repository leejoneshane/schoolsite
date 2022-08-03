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
        return $this->morphMany('App\Models\Gsuite', 'owner');
	}
    
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'class_id');
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
