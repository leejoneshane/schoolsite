<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameProcess extends Model
{

    protected $table = 'game_processes';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',         //冒險者
        'adventure_id', //冒險代號
        'worksheet_id', //學習單代號
        'task_id',      //任務編號
        'completed_at', //完成時間
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'character',
        'adventure',
        'worksheet',
        'task',
    ];

    //篩選指定學生、指定學習單的完成紀錄
    public static function findBy($uuid, $adventure_id)
    {
        return GameProcess::where('uuid', $uuid)
            ->where('adventure_id', $adventure_id)
            ->orderBy('completed_at')
            ->get();
    }

    public function character()
    {
        return $this->hasOne('App\Models\GameCharacter', 'uuid', 'uuid');
    }

    public function adventure()
    {
        return $this->hasOne('App\Models\GameAdventure', 'id', 'adventure_id');
    }

    public function worksheet()
    {
        return $this->hasOne('App\Models\GameWorksheet', 'id', 'worksheet_id');
    }

    public function task()
    {
        return $this->hasOne('App\Models\GameTask', 'id', 'task_id');
    }
}
