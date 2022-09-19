<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubEnroll extends Model
{

    protected $table = 'students_clubs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year',
        'uuid',
        'club_id',
        'need_lunch',
        'weekdays',
        'identity',
        'email',
        'parent',
        'mobile',
        'accepted',
        'audited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'need_lunch' => 'boolean',
        'weekdays' => 'array',
        'accepted' => 'boolean',
        'audited_at' => 'datetime:Y-m-d H:i:s',
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

    public static function current_year()
	{
    	if (date('m') > 7) {
        	$year = date('Y') - 1911;
    	} else {
        	$year = date('Y') - 1912;
    	}
    	return $year;
	}

    public function club()
    {
        return $this->belongsTo('App\Models\Club', 'club_id');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'uuid');
    }

}
