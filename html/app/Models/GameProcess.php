<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameProcess extends Model
{

    protected $table = 'game_processes';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',         //探險者
        'classroom_id', //答題者班級代號
        'seat',         //答題者座號
        'student',      //答題者姓名
        'adventure_id', //探險代號
        'worksheet_id', //學習單代號
        'task_id',      //任務編號
        'completed_at', //完成時間
        'comments',     //評語
        'noticed',      //需要重審
        'reviewed_at',  //審核通過時間
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'character',
        'classroom',
        'adventure',
        'worksheet',
        'task',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'noticed' => 'boolean',
        'completed_at' => 'datetime:Y-m-d H:i:s',
        'reviewed_at' => 'datetime:Y-m-d H:i:s',
    ];

    //篩選指定班級、指定任務的完成紀錄
    public static function findByClassroom($room_id, $adventure_id, $task_id)
    {
        return GameProcess::where('classroom_id', $room_id)
            ->where('adventure_id', $adventure_id)
            ->where('task_id', $task_id)
            ->orderBy('seat')
            ->get();
    }

    //篩選指定學生、指定學習單的完成紀錄
    public static function findByUuid($uuid, $adventure_id)
    {
        return GameProcess::where('uuid', $uuid)
            ->where('adventure_id', $adventure_id)
            ->orderBy('completed_at')
            ->get();
    }

    //篩選指定學生、指定學習單的完成紀錄
    public static function findByTask($uuid, $adventure_id, $task_id)
    {
        return GameProcess::where('uuid', $uuid)
            ->where('adventure_id', $adventure_id)
            ->where('task_id', $task_id)
            ->first();
    }

    public function character()
    {
        return $this->hasOne('App\Models\GameCharacter', 'uuid', 'uuid');
    }

    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'id', 'classroom_id');
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
