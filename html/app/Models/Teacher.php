<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Providers\TpeduServiceProvider as SSO;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Role;
use App\Models\Domain;
use App\Models\Classroom;

class Teacher extends Model
{

    use SoftDeletes;

    protected $table = 'teachers';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
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

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'user',
        'gmails',
        'unit',
        'units',
        'role',
        'roles',
        'domain',
        'domains',
        'mainunit',
        'tutor_classroom',
        'subjects',
        'classrooms',
        'seniority',
        'last_survey',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'mainunit',
        'tutor',
        'domain',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'birthdate' => 'datetime:Y-m-d',
    ];

    //提供教師的主要單位
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

    //提供導師班級
    public function getTutorAttribute()
    {
        if ($this->tutor_class) {
            return Classroom::find($this->tutor_class)->name;
        }
        return false;
    }

    //提供教師隸屬領域
    public function getDomainAttribute()
    {
        $domain = DB::table('belongs')->where('uuid', $this->uuid)->where('year', current_year())->first();
        if ($domain && $domain->id) {
            return Domain::find($domain->id);
        }
        return false;
    }

    //取得所有行政人員
    public static function admins()
    {
        $roles = Role::administrator()->pluck('id');
        return Teacher::selectRaw('teachers.*, LEFT(units.unit_no, 3) as top')
            ->leftjoin('units', 'units.id', '=', 'teachers.unit_id')
            ->leftjoin('roles', 'roles.id', '=', 'teachers.role_id')
            ->whereIn('teachers.role_id', $roles)
            ->orderBy('top')
            ->orderBy('roles.role_no')
            ->orderBy('roles.name')
            ->get();
    }

    //取得所有主任
    public static function director()
    {
        $roles = Role::director()->pluck('id');
        return Teacher::leftjoin('units', 'units.id', '=', 'teachers.unit_id')
            ->whereIn('teachers.role_id', $roles)
            ->orderBy('units.unit_no')
            ->get();
    }

    //取得所有非主任行政人員（包含：組長、特殊任務）
    public static function manager()
    {
        $roles = Role::manager()->pluck('id');
        return Teacher::selectRaw('teachers.*, LEFT(units.unit_no, 3) as top')
            ->leftjoin('units', 'units.id', '=', 'teachers.unit_id')
            ->leftjoin('roles', 'roles.id', '=', 'teachers.role_id')
            ->whereIn('teachers.role_id', $roles)
            ->orderBy('top')
            ->orderBy('roles.role_no')
            ->orderBy('roles.name')
            ->get();
    }

    //取得級任和科任教師(不含行政人員)
    public static function general()
    {
        $roles = Role::general()->pluck('id');
        return Teacher::whereIn('role_id', $roles)
            ->orderBy('tutor_class')
            ->get();
    }

    //取得指定全誼系統代號的教師
    public static function findById($id)
    {
        return Teacher::where('id', $id)->first();
    }

    //取得指定身分證字號的教師
    public static function findByIdno($idno)
    {
        return Teacher::where('idno', $idno)->first();
    }

    //取得指定班級的導師
    public static function findByClass($class_id)
    {
        return Teacher::where('tutor_class', $class_id)->first();
    }

    //取得教師所屬單位
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    //取得教師主要職務
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    //取得教師的使用者帳戶
    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    //取得教師的 Gsuite 帳戶
    public function gmails()
    {
        return $this->morphMany('App\Models\Gsuite', 'owner');
    }

    //取得教師的導師班級
    public function tutor_classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'tutor_class');
    }

    //取得教師的所有單位（不含上層單位）
    public function units()
    {
        return $this->belongsToMany('App\Models\Unit', 'job_title', 'uuid', 'unit_id')->where('year', current_year());
    }

    //取得教師的所有單位（包含上層單位）
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

    //取得教師的所有上層單位
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

    //取得教師的所有職務
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'job_title', 'uuid', 'role_id')->wherePivot('year', current_year());
    }

    //取得教師的所有配課
    public function assignment()
    {
        return DB::table('assignment')->where('year', current_year())->where('uuid', $this->uuid)->get();
    }

    //取得教師的隸屬領域
    public function domains()
    {
        return $this->belongsToMany('App\Models\Domain', 'belongs', 'uuid', 'domain_id')->wherePivot('year', current_year());
    }

    //取得教師的所有配課科目
    public function subjects()
    {
        return $this->belongsToMany('App\Models\Subject', 'assignment', 'uuid', 'subject_id')->wherePivot('year', current_year());
    }

    //取得教師的所有任教班級
    public function classrooms()
    {
        return $this->belongsToMany('App\Models\Classroom', 'assignment', 'uuid', 'class_id')->wherePivot('year', current_year());
    }

    //取得教師的年資積分
    public function seniority()
    {
        return $this->hasOne('App\Models\Seniority', 'uuid', 'uuid')->where('syear', current_year());
    }

    //取得教師的職編意願調查表
    public function survey($year = null)
    {
        if (!$year) $year = current_year();
        return $this->hasOne('App\Models\OrganizeSurvey', 'uuid', 'uuid')->where('syear', $year)->first();
    }

    //取得教師最新的職編意願調查表
    public function last_survey()
    {
        return $this->hasOne('App\Models\OrganizeSurvey', 'uuid', 'uuid')->latest();
    }

    //重新從 LDAP 同步教師個資
    public function sync()
    {
        $sso = new SSO;
        $sso->fetch_user($this->uuid);
        $this->fresh();
    }

    //檢查此教師的同步資料是否已經過期
    public function expired()
    {
        $expire = new Carbon($this->updated_at);
        return Carbon::today() > $expire->addDays(config('services.tpedu.expired_days'));
    }

}
