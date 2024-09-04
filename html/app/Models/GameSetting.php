<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Game extends Model
{

    protected $table = 'game_settings';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'settings',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'settings' => AsCollection::class,
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'teacher',
    ];

    //取得此遊戲的管理教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
