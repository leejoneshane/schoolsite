<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameBase extends Model
{

    protected $table = 'game_bases';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',        //據點名稱
        'image_file',
        'hp',          //對成員的健康值增減效益，2 則加 2 點，0.5 則加 50%，-1 為扣 1 點，-0.3 為扣 30%
        'mp',          //對成員的行動力增減效益
        'ap',          //對成員的攻擊力增減效益
        'dp',          //對成員的防禦力增減效益
        'sp',          //對成員的敏捷力增減效益
    ];

}
