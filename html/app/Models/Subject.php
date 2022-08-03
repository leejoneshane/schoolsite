<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

class Subject extends Model
{

	protected $table = 'subjects';
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
        'name',
    ];

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'assigment', 'subject_id', 'uuid');
    }

    public function classrooms()
    {
        return $this->belongsToMany('App\Models\Classroom', 'assigment', 'subject_id', 'class_id');
    }

}
