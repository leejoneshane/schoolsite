<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;

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
        'id',
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

    protected $appends = [
        'mainunit',
        'unit',
        'role',
        'tutor',
    ];

    public function getMainunitAttribute()
    {
        $unit = Unit::find($this->unit_id);
        if (!$unit) return null;
        if ($unit->is_main()) {
            return $unit;
        } else {
            return $unit->uplevel();
        }
    }

    public function getUnitAttribute()
    {
        return Unit::find($this->unit_id);
    }

    public function getRoleAttribute()
    {
        return Role::find($this->role_id);
    }

    public function getTutorAttribute()
    {
        if ($this->tutor_class) {
            return Classroom::find($this->tutor_class)->name;
        }
        return false;
    }

    public static function current_year()
    {
        if (date('m') > 7) {
            $year = date('Y') - 1911;
        } else {
            $year = date('Y') - 1912;
        }
        return $year;
    }

    public static function findById($id) //全誼系統代號
    {
        return Teacher::where('id', $id)->first();
    }

    public static function findByIdno($idno) //身分證字號
    {
        return Teacher::where('idno', $idno)->first();
    }

    public static function findByClass($class_id) //任教年班
    {
        return Teacher::where('tutor_class', $class_id)->first();
    }

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
        return $this->belongsToMany('App\Models\Unit', 'job_title', 'uuid', 'unit_id')->where('year', Teacher::current_year());
    }

    public function union()
    {
        $uni = [];
        $units = $this->units;
        foreach ($units as $u) {
            $uni[] = $u;
            if (!$u->is_main()) {
                $uni[] = $u->uplevel();
            }
        }
        return $uni;
    }

    public function upper()
    {
        $upper = [];
        $units = $this->units;
        foreach ($units as $u) {
            if ($u->is_main()) {
                $upper[] = $u;
            } else {
                $upper[] = $u->uplevel();
            }
        }
        $upper = array_unique($upper);
        return $upper;
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'job_title', 'uuid', 'role_id')->where('year', Teacher::current_year());
    }

    public function assignment()
    {
        return DB::table('assignment')->where('year', Teacher::current_year())->where('uuid', $this->uuid)->get();
    }

    public function subjects()
    {
        return $this->belongsToMany('App\Models\Subject', 'assignment', 'uuid', 'subject_id')->where('year', Teacher::current_year());
    }

    public function classrooms()
    {
        return $this->belongsToMany('App\Models\Classroom', 'assignment', 'uuid', 'class_id')->where('year', Teacher::current_year());
    }

    public function seniority()
    {
        return $this->hasOne('App\Models\Seniority', 'uuid', 'uuid')->where('syear', Teacher::current_year());
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
