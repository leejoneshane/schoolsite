<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\TpeduServiceProvider as SSO;

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
        return $this->hasMany('App\Models\Subscriber', 'news_id', 'id');
    }

}
