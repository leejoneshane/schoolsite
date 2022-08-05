<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Classroom extends Model
{

	protected $table = 'classrooms';
	protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'grade_id',
        'tutor',
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tutor' => 'array',
    ];

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }

    public function tutors()
    {
        return $this->hasMany('App\Models\Teacher', 'tutor_class');
    }

    public function students()
    {
        return $this->hasMany('App\Models\Student', 'class_id');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'assigment', 'class_id', 'uuid');
    }

    public function subjects()
    {
        return $this->belongsToMany('App\Models\Subject', 'assigment', 'class_id', 'subject_id');
    }

}
