<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{

	protected $table = 'club_kinds';

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

    public function kind()
    {
        return $this->belongsTo('App\Models\ClubKind');
    }

    public function enrolls()
    {
        return $this->hasMany('App\Models\ClubEnroll', 'club_id');
    }

    public function enrolled()
    {
        return $this->hasMany('App\Models\ClubEnroll', 'club_id')->where('accepted', 1);
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

    public function enrolled_students($year)
    {
        return $this->students()->wherePivot('year', $year)->wherePivot('accepted', 1)->get();
    }

}
