<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

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

    public function grade()
    {
        return $this->belongsTo('App\Models\Grade', 'id', 'grade');
    }

    public function tutor()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'tutor');
    }

    public function students()
    {
        return $this->hasMany('App\Models\Student', 'class_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'assigment', 'class_id', 'uuid');
    }

    public function subjects()
    {
        return $this->belongsToMany('App\Models\Subject', 'assigment', 'class_id', 'subject_id');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

}
