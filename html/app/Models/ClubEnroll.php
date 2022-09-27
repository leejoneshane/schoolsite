<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ClubNotification;
use App\Notifications\ClubEnrollNotification;
use App\Notifications\ClubEnrolledNotification;

class ClubEnroll extends Model
{
    use Notifiable;

    protected $table = 'students_clubs';

    protected static $weekMap = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];

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

    protected $appends = [
		'weekday',
    ];

    public function getWeekdayAttribute()
    {
        sort($this->weekdays); 
        $str = [];
        foreach ($this->weekdays as $g) {
            $str[] = self::$weekMap[$g];
        }
        return '每週'.implode('、', $str);
    }

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

    public function year_order()
    {
        return ClubEnroll::where('club_id', $this->club_id)->where('year', $this->year)->where('created_at', '<', $this->created_at)->count();
    }

    public function sendClubNotification($message)
    {
        $this->notify(new ClubNotification($message));
    }

    public function sendClubEnrollNotification()
    {
        $this->notify(new ClubEnrollNotification);
    }

    public function sendClubEnrolledNotification()
    {
        $this->notify(new ClubEnrolledNotification);
    }

}
