<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{

	protected $table = 'news_letters';

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

    public function subscribers()
    {
        return $this->belongsToMany('App\Models\Subscriber', 'news_subscribers', 'news_id', 'subscriber_id')->whereNotNull('email_verified_at')->as('subscription')->withTimestamps();
    }

}
