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

    public static function current_year()
    {
        if (date('m') > 7) {
            $year = date('Y') - 1911;
        } else {
            $year = date('Y') - 1912;
        }
        return $year;
    }

    public function teachersByYear($year)
    {
        return $this->belongsToMany('App\Models\Teacher', 'belongs', 'domain_id', 'uuid')->where('year', $year)->get();
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'belongs', 'domain_id', 'uuid')->where('year', Domain::current_year());
    }

}
