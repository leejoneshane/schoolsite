<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;
use Carbon\Carbon;
use App\Models\Grade;
use App\Models\ClubEnroll;

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

    public function year_enrolls($year = null)
    {
        if ($year) {
            return $this->enrolls()->where('year', $year)->get();
        } else {
            return $this->enrolls()->where('year', CLubEnroll::current_year())->get();
        }
    }

    public function current_enrolls_for_kind($kind_id)
    {
        $filtered = $this->year_enrolls()->filter(function ($enroll) use ($kind_id) {
            return $enroll->club->kind_id == $kind_id;
        });
        return $filtered;
    }

    public function get_enroll($club_id)
    {
        return ClubEnroll::findBy($this->uuid, $club_id);
    }

    public function has_enroll($club_id)
    {
        $rec = ClubEnroll::findBy($this->uuid, $club_id);
        return ($rec) ? true : false;
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
