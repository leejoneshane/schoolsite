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

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'model',
        'cron',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'subscribers',
        'verified',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'job',
        'loop',
    ];

    //提供派報時間字串
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

    //提供派報時間陣列
    public function getLoopAttribute()
    {
        list($loop, $day) = explode('.', $this->cron);
        return [ 'loop' => $loop, 'day' => $day];
    }

    //取得此電子報的所有訂閱戶
    public function subscribers()
    {
        return $this->belongsToMany('App\Models\Subscriber', 'news_subscribers', 'news_id', 'subscriber_id')->as('subscription')->withTimestamps();
    }

    //取得此電子報所有已驗證郵件地址的訂閱戶
    public function verified()
    {
        return $this->belongsToMany('App\Models\Subscriber', 'news_subscribers', 'news_id', 'subscriber_id')->whereNotNull('email_verified_at')->as('subscription')->withTimestamps();
    }

}
