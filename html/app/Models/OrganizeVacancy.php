<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;

class OrganizeVacancy extends Model
{

    protected $table = 'organize_vacancy';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'syear',
        'type', //'admin', 'tutor', 'domain'
        'role_id',
        'grade_id',
        'domain_id',
        'special',
        'name',
        'stage',
        'shortfall',
        'filled',
        'assigned',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'special' => 'boolean',
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

    public static function current()
    {
        return OrganizeVacancy::where('syear', current_year())->get();
    }

    public static function current_type($type)
    {
        return OrganizeVacancy::where('syear', current_year())->where('type', $type)->get();
    }

    public static function current_stage($stage)
    {
        $general = OrganizeVacancy::where('syear', current_year())->where('stage', $stage)->where('special', false)->get();
        $special = OrganizeVacancy::where('syear', current_year())->where('stage', $stage)->where('special', true)->get();
        return (object) array('general' => $general, 'special' => $special);
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }

    public function original()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_original', 'vacancy_id', 'uuid')->where('syear', $this->syear)->get();
    }

    public function reserved()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_reserved', 'vacancy_id', 'uuid')->where('syear', $this->syear)->get();
    }

    public function swaping()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_swap', 'vacancy_id', 'uuid')->where('syear', $this->syear)->get();
    }

    public function assigned()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_assign', 'vacancy_id', 'uuid')->where('syear', $this->syear)->get();
    }

}
