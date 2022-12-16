<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gsuite extends Model
{
    protected $table = 'gsuite';

    //以下屬性可以批次寫入
    protected $fillable = [
        'owner_id',
        'owner_type',
        'userKey',
        'primary', //是否為主要電子郵件（非別名）
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'primary' => 'boolean',
    ];

    //取得此 Gsuite 帳號的擁有者（教師或學生物件）
    public function owner()
    {
        return $this->morphTo();
    }

}