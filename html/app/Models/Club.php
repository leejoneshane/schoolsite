<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClubEnroll;
use Carbon\Carbon;

class Club extends Model
{

	protected $table = 'clubs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'kind_id',
        'unit_id',
        'for_grade',
        'weekdays',
        'self_defined',
        'self_remove',
        'has_lunch',
        'stop_enroll',
        'startDate',
        'endDate',
        'startTime',
        'endTime',
        'teacher',
        'location',
        'memo',
        'cash',
        'total',
        'maximum',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'for_grade' => 'array',
        'weekdays' => 'array',
        'self_defined' => 'boolean',
        'self_remove' => 'boolean',
        'has_lunch' => 'boolean',
        'stop_enroll' => 'boolean',
        'startDate' => 'datetime:Y-m-d',
        'endDate' => 'datetime:Y-m-d',
    ];

    protected $appends = [
		'grade',
        'studytime',
        'style',
    ];

    protected static $weekMap = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];

    public function getGradeAttribute()
    {
        $str = '';
        foreach ($this->for_grade as $g) {
            $str .= self::$weekMap[$g];
        }
        return $str.'年級';
    }

    public function getStudytimeAttribute()
    {
        $str ='';
        $str .= substr($this->startDate, 0, 10);
        $str .= '～';
        $str .= substr($this->endDate, 0, 10);
        if ($this->self_defined) {
            $str .= ' 每週上課日由家長自選';
        } else {
            $str .= ' 每週';
            foreach ($this->weekdays as $d) {
                $str .= self::$weekMap[$d];
            }
        }
        $str .= ' ';
        $str .= $this->startTime;
        $str .= '～';
        $str .= $this->endTime;
        return $str;
    }

    public function getStyleAttribute()
    {
        return $this->kind->style;
    }

    public static function can_enroll($grade = null)
    {
        $today = Carbon::now();
        if ($grade) {
            return Club::leftjoin('club_kinds', 'clubs.kind_id', '=', 'club_kinds.id')
            ->select('clubs.*', 'club_kinds.style')
            ->where('club_kinds.stop_enroll', false)
            ->whereDate('club_kinds.enrollDate', '<=', $today)
            ->whereDate('club_kinds.expireDate', '>=', $today)
            ->where('clubs.stop_enroll', false)
            ->whereJsonContains('clubs.for_grade', $grade)
            ->orderBy('clubs.kind_id')
            ->get();
        } else {
            return Club::leftjoin('club_kinds', 'clubs.kind_id', '=', 'club_kinds.id')
            ->select('clubs.*', 'club_kinds.style')
            ->where('club_kinds.stop_enroll', false)
            ->whereDate('club_kinds.enrollDate', '<=', $today)
            ->whereDate('club_kinds.expireDate', '>=', $today)
            ->where('clubs.stop_enroll', false)
            ->orderBy('clubs.kind_id')
            ->get();
        }
    }

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

    public function kind()
    {
        return $this->belongsTo('App\Models\ClubKind');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll')->orderBy('created_at');
    }

    public function accepted_enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll')->where('accepted', true)->orderBy('created_at');
    }

    public function year_enrolls($year = null)
    {
        if ($year) {
            return $this->enrolls()->where('year', $year)->get();
        } else {
            return $this->enrolls()->where('year', current_year())->get();
        }
    }

    public function year_accepted($year = null)
    {
        if ($year) {
            return $this->accepted_enrolls()->where('year', $year)->get();
        } else {
            return $this->accepted_enrolls()->where('year', current_year())->get();
        }
    }

    public function count_enrolls()
    {
        return $this->year_enrolls()->count();
    }

    public function count_accepted()
    {
        return $this->year_accepted()->count();
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'students_clubs', 'club_id', 'uuid')
            ->as('enroll')
            ->withPivot([
                'id',
                'year',
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

    public function enroll_students($year)
    {
        return $this->students()->wherePivot('year', $year)->get();
    }

    public function accepted_students($year)
    {
        return $this->students()->wherePivot('year', $year)->wherePivot('accepted', 1)->get();
    }

}
