<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasCompositePrimaryKey;

class Seniority extends Model
{

    use HasCompositePrimaryKey;
    
    protected $table = 'seniority';
    protected $primaryKey = ['uuid', 'syear'];
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'syear',
        'no',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ok' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = self::current_year();
            }
        });
    }

    public function getYearsAttribute()
    {
        return round(($this->school_year * 12 + $this->teach_year * 12 + $this->school_month + $this->teach_month) / 12, 2);
    }

    public function getScoreAttribute()
    {
        return $this->school_score + $this->teach_score;
    }

    public function getNewyearsAttribute()
    {
        return round(($this->new_school_year * 12 + $this->new_teach_year * 12 + $this->new_school_month + $this->new_teach_month) / 12, 2);
    }

    public function getNewscoreAttribute()
    {
        return $this->new_school_score + $this->new_teach_score;
    }

    public static function findBy($uuid, $syear)
    {
        return Seniority::where('uuid', $uuid)->where('syear', $syear)->first();
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
        return DB::table('seniority')->select(['syear'])->distinct()->get()->map(function ($item) {
            return $item->syear;
        })->toArray();
    }

    public static function current()
    {
        return Seniority::where('syear', Seniority::current_year())->orderBy('no')->get();
    }

    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
