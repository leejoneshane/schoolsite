<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairJob extends Model
{

	protected $table = 'repair_jobs';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'kind_id',
        'place',
        'summary',
        'description',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'reporter',
        'kind',
        'replys',
        'reply',
    ];

    //取得報修人員
    public function reporter()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此報修紀錄的分類
    public function kind()
    {
        return $this->belongsTo('App\Models\RepairKind', 'id', 'kind_id');
    }

    //取得此報修紀錄的所有回覆（由舊到新）
    public function replys()
    {
        return $this->hasMany('App\Models\RepairReply', 'job_id')->oldest();
    }

    //取得此報修紀錄的最後一筆回覆
    public function reply()
    {
        return $this->hasOne('App\Models\RepairReply', 'job_id')->latestOfMany();
    }

}
