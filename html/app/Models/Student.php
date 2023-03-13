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

    //SoftDeletes: 啟用軟性刪除功能
    use SoftDeletes;

    protected $table = 'students';
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

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'user',
        'gmails',
        'grade',
        'classroom',
        'enrolls',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'stdno',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'birthdate' => 'datetime:Y-m-d',
    ];

    //提供學生名牌號碼
    public function getStdnoAttribute()
    {
        return $this->class_id . (($this->seat < 10) ? '0'.$this->seat : $this->seat);
    }

    //篩選指定學號的學生
    public static function findById($id)
    {
        return Student::where('id', $id)->first();
    }

    //篩選指定身分證字號的學生
    public static function findByIdno($idno)
    {
        return Student::where('idno', $idno)->first();
    }

    //根據年班和座號篩選學生
    public static function findByStdno($class_id, $seat)
    {
        return Student::where('class_id', $class_id)->where('seat', $seat)->first();
    }

    //取得此學生的使用者帳戶
    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'uuid')->withDefault();
    }

    //取得此學生的 Gsuite 帳戶
    public function gmails()
    {
        return $this->morphMany('App\Models\Gsuite', 'owner');
    }

    //取得此學生就讀年級
    public function grade()
    {
        return Grade::find(substr($this->class_id, 0, 1));
    }

    //取得此學生就讀班級
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'class_id');
    }

    //取得此學生所有社團報名資訊
    public function enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll', 'uuid', 'uuid');
    }

    //取得此學生指定學年的所有社團報名資訊
    public function section_enrolls($section = null)
    {
        if ($section) {
            return $this->enrolls()->where('section', $section)->get();
        } else {
            return $this->enrolls()->where('section', current_section())->get();
        }
    }

    //取得此學生指定分類的所有社團報名資訊
    public function current_enrolls_for_kind($kind_id)
    {
        $filtered = $this->section_enrolls()->filter(function ($enroll) use ($kind_id) {
            return $enroll->club->kind_id == $kind_id;
        });
        return $filtered;
    }

    //取得此學生指定社團的報名資訊
    public function get_enroll($club_id)
    {
        return ClubEnroll::findBy($this->uuid, $club_id);
    }

    //檢查此學生是否報名指定社團
    public function has_enroll($club_id)
    {
        $rec = ClubEnroll::findBy($this->uuid, $club_id);
        return ($rec) ? true : false;
    }

    //重新從 LDAP 同步學生個資
    public function sync()
    {
        $sso = new SSO;
        $sso->fetch_user($this->uuid);
        $this->refresh();
    }

    //檢查此學生的同步資料是否已經過期
    public function expired()
    {
        $expire = new Carbon($this->updated_at);
        return Carbon::today() > $expire->addDays(config('services.tpedu.expired_days'));
    }

}
