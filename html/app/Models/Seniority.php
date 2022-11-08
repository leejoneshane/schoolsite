<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seniority extends Model
{

    protected $table = 'seniority';
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
        'year',
        'school_year',
        'school_month',
        'school_score',
        'teach_year',
        'teach_month',
        'teach_score',
        'ok',
        'new_school_year',
        'new_school_month',
        'new_school_score',
        'new_teach_year',
        'new_teach_month',
        'new_teach_score',
    ];

    protected $appends = [
        'years',
        'score',
        'newyears',
        'newscore',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->year)) {
                $model->year = self::current_year();
            }
        });
    }

    public function getYearsAttribute()
    {
        return ($this->school_year * 12 + $this->teach_year * 12 + $this->school_month + $this->teach_month) / 12;
    }

    public function getScoreAttribute()
    {
        return $this->school_score + $this->teach_score;
    }

    public function getNewyearsAttribute()
    {
        return ($this->new_school_year * 12 + $this->new_teach_year * 12 + $this->new_school_month + $this->new_teach_month) / 12;
    }

    public function getNewscoreAttribute()
    {
        return $this->new_school_score + $this->new_teach_score;
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

    public static function years()
    {
        return Seniority::select(['year'])->distinct()->get()->toArray();
    }

    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
