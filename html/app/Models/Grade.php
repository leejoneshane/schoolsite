<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{

	protected $table = 'grades';
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

    public function classrooms()
    {
        return $this->hasMany('App\Models\Classroom');
    }

}
