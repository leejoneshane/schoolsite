<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialiteAccount extends Model
{
    protected $table = 'socialite_account';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid', 'socialite', 'userID',
    ];

    //取得此社群帳戶綁定的使用者
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'uuid', 'uuid');
    }
}