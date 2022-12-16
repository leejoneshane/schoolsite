<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{

    protected $table = 'domains';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'name',
    ];

    //取得指定學年的領域教師
    public function year($year)
    {
        return $this->belongsToMany('App\Models\Teacher', 'belongs', 'domain_id', 'uuid')->where('year', $year)->get();
    }

    //取得目前所有領域教師
    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'belongs', 'domain_id', 'uuid')->where('year', current_year());
    }

}
