<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairReply extends Model
{

	protected $table = 'repair_jobs';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'job_id',
        'status',
        'comment',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'maintener',
        'job',
    ];

    //取得報修人員
    public function maintener()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此報修紀錄的分類
    public function job()
    {
        return $this->belongsTo('App\Models\RepairJob', 'id', 'job_id');
    }

}
