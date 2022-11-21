<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{

    protected $table = 'domains';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
    ];

    public function teachersByYear($year)
    {
        return $this->belongsToMany('App\Models\Teacher', 'belongs', 'domain_id', 'uuid')->where('year', $year)->get();
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'belongs', 'domain_id', 'uuid')->where('year', current_year());
    }

}
