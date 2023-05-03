<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClubSection;
use Carbon\Carbon;

class Club extends Model
{

	protected $table = 'clubs';

    protected static $weekMap = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'short_name',
        'kind_id',
        'unit_id',
        'for_grade',
        'self_remove',
        'has_lunch',
        'stop_enroll',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'sections',
        'kind',
        'unit',
        'enrolls',
        'accepted_enrolls',
        'students',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'for_grade' => 'array',
        'self_remove' => 'boolean',
        'has_lunch' => 'boolean',
        'stop_enroll' => 'boolean',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
		'grade',
        'style',
    ];

    //提供可報名年級中文字串
    public function getGradeAttribute()
    {
        $str = '';
        foreach ($this->for_grade as $g) {
            $str .= self::$weekMap[$g];
        }
        return $str.'年級';
    }

    //提供此社團的分類樣式
    public function getStyleAttribute()
    {
        return $this->kind->style;
    }

    //篩選可報名的社團（可依年級篩選），靜態函式
    public static function can_enroll($grade = null)
    {
        $today = Carbon::now();
        if ($grade) {
            return Club::select('clubs.*', 'club_kinds.style')
            ->leftjoin('club_kinds', 'clubs.kind_id', '=', 'club_kinds.id')
            ->leftjoin('clubs_section', 'clubs.id', '=', 'clubs_section.club_id')
            ->where('clubs_section.section', current_section())
            ->where('club_kinds.stop_enroll', false)
            ->whereDate('club_kinds.enrollDate', '<=', $today)
            ->whereDate('club_kinds.expireDate', '>=', $today)
            ->where('clubs.stop_enroll', false)
            ->whereJsonContains('clubs.for_grade', (integer) $grade)
            ->orderBy('clubs.kind_id')
            ->get();
        } else {
            return Club::select('clubs.*', 'club_kinds.style')
            ->leftjoin('club_kinds', 'clubs.kind_id', '=', 'club_kinds.id')
            ->leftjoin('clubs_section', 'clubs.id', '=', 'clubs_section.club_id')
            ->where('clubs_section.section', current_section())
            ->where('club_kinds.stop_enroll', false)
            ->whereDate('club_kinds.enrollDate', '<=', $today)
            ->whereDate('club_kinds.expireDate', '>=', $today)
            ->where('clubs.stop_enroll', false)
            ->orderBy('clubs.kind_id')
            ->get();
        }
    }

    //篩選本學年需進行收費統計的社團，靜態函式
    public static function cash_enroll()
    {
        if (date('m') > 7) {
            $min = date('Y').'-8-1';
            $max = (date('Y') + 1).'-7-31';
        } else {
            $min = (date('Y') - 1).'-8-1';
            $max = date('Y').'-7-31';
        }
        return Club::leftjoin('club_kinds', 'clubs.kind_id', '=', 'club_kinds.id')
        ->select('clubs.*', 'club_kinds.style')
        ->whereDate('club_kinds.enrollDate', '>=', $min)
        ->whereDate('club_kinds.expireDate', '<=', $max)
        ->orderBy('clubs.kind_id')
        ->get();
    }

    //取得此社團的學期資訊
    public function sections()
    {
        return $this->hasMany('App\Models\ClubSection', 'club_id')->orderBy('section', 'desc');
    }

    //提供目前的學期資訊
    public function section($section = null)
    {
        if ($section) {
            return ClubSection::where('club_id', $this->id)
            ->where('section', $section)
            ->first();
        } else {
            return ClubSection::where('club_id', $this->id)
            ->where('section', current_section())
            ->first();
        }
    }

    //取得此社團的分類
    public function kind()
    {
        return $this->belongsTo('App\Models\ClubKind');
    }

    //取得此社團的主辦單位
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    //取得此社團所有的報名資訊，依報名時間排序
    public function enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll')->orderBy('created_at');
    }

    //取得此社團所有已錄取的報名資訊，依報名時間排序
    public function accepted_enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll')->where('accepted', true)->orderBy('created_at');
    }

    //取得此社團指定學年或本學年所有的報名資訊，依報名時間排序
    public function section_enrolls($section = null)
    {
        if ($section) {
            return $this->enrolls()->where('section', $section)->get();
        } else {
            return $this->enrolls()->where('section', current_section())->get();
        }
    }
    //取得此社團指定學年或本學年所有已錄取的報名資訊，依報名時間排序
    public function section_accepted($section = null)
    {
        if ($section) {
            return $this->accepted_enrolls()->where('section', $section)->get();
        } else {
            return $this->accepted_enrolls()->where('section', current_section())->get();
        }
    }

    //計算此社團本學年報名學生數
    public function count_enrolls($section = null)
    {
        return $this->section_enrolls($section)->count();
    }

    //計算此社團本學年錄取學生數
    public function count_accepted($section = null)
    {
        return $this->section_accepted($section)->count();
    }

    //取得此社團所有的學生
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'clubs_students', 'club_id', 'uuid')
            ->withPivot([
                'id',
                'section',
                'need_lunch',
                'weekdays',
                'identity',
                'email',
                'parent',
                'mobile',
                'accepted',
                'audited_at',
                'created_at',
                'updated_at',
            ]);
    }

    //取得此社團指定學年或本學年所有的學生
    public function enroll_students($section = null)
    {
        if ($section) {
            return $this->students()->wherePivot('section', $section)->get();
        } else {
            return $this->students()->wherePivot('section', current_section())->get();
        }
    }

    //取得此社團指定學年或本學年所有已錄取的學生
    public function accepted_students($section = null)
    {
        if ($section) {
            return $this->students()->wherePivot('section', $section)->wherePivot('accepted', 1)->get();
        } else {
            return $this->students()->wherePivot('section', current_section())->wherePivot('accepted', 1)->get();
        }
    }

}
