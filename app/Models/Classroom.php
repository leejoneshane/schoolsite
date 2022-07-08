<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpedussoServiceProvider as SSO;

class Classroom extends Model
{

	protected $table = 'classrooms';
	protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'tutor_id',
        'name',
    ];

    public function tutor()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'tutor_id');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'assigment', 'class_id', 'uuid');
    }

    public function subjects()
    {
        return $this->belongsToMany('App\Models\Subject', 'assigment', 'class_id', 'subj_id');
    }

    public function sync()
    {
        $sso = new SSO();
        // todo
    }

}
