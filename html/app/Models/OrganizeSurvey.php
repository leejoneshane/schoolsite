<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizeSurvey extends Model
{

    protected $table = 'organize_survey';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'syear',
        'uuid',
        'age',
        'exprience',
        'edu_level',
        'edu_school',
        'edu_division',
        'score',
        'admin1',
        'admin2',
        'admin3',
        'special',
        'teach1',
        'teach2',
        'teach3',
        'teach4',
        'teach5',
        'teach6',
        'grade',
        'overcome',
        'assign',
    ];

    protected $appends = [
        'specials',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = current_year();
            }
        });
    }

    public function getSpecialsAttribute()
    {
        return explode(',', $this->special);
    }

    public static function current()
    {
        return OrganizeSurvey::where('syear', current_year())->get();
    }

    public static function findByUUID($uuid)
    {
        return OrganizeSurvey::where('syear', current_year())->where('uuid', $uuid)->first();
    }

    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
