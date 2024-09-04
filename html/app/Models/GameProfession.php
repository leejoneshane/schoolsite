<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameProfession extends Model
{

    protected $table = 'game_professions';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'levelup',
        'base_hp',
        'base_mp',
        'base_ap',
        'base_dp',
        'base_sp',
        'skills',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'levelup' => 'array',
        'skills' => 'array',
    ];

}
