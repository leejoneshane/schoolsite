<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{

	protected $table = 'news_letters';
    public $timestamps = false;

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
        'name',
        'model',
        'cron',
    ];

    protected $appends = [
        'job',
        'loop',
    ];

    public function getJobAttribute()
    {
        list($loop, $day) = explode('.', $this->cron);
        if ($loop == 'monthly') {
            return '每月'.$day.'日';
        }
        if ($loop == 'weekly') {
            return '每週'.self::$weekMap[$day];
        }
        return '自動';
    }

    public function getLoopAttribute()
    {
        list($loop, $day) = explode('.', $this->cron);
        return [ 'loop' => $loop, 'day' => $day];
    }

    public function subscribers()
    {
        return $this->belongsToMany('App\Models\Subscriber', 'news_subscribers', 'news_id', 'subscriber_id')->as('subscription')->withTimestamps();
    }

    public function verified()
    {
        return $this->belongsToMany('App\Models\Subscriber', 'news_subscribers', 'news_id', 'subscriber_id')->whereNotNull('email_verified_at')->as('subscription')->withTimestamps();
    }

}
