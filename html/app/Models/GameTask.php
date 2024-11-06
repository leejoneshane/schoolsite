<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameTask extends Model
{

    protected $table = 'game_tasks';

    //以下屬性可以批次寫入
    protected $fillable = [
        'title',        //任務標題
        'worksheet_id', //學習單編號
        'next_task',    //下一個任務的 id
        'coordinate_x', //X座標
        'coordinate_y', //Y座標
        'story',        //故事
        'task',         //學習任務
        'review',       //是否需要教師審核
        'reward_xp',    //完成後的經驗值獎勵
        'reward_gp',    //完成後的金幣獎勵
        'reward_item',  //完成後的道具獎勵
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'worksheet',
        'prev',
        'next',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'is_orphan',
    ];

    //篩選指定學習單的所有學習任務
    public static function findByWorksheet($id)
    {
        return GameTask::where('worksheet_id', $id)->orderBy('id')->get();
    }

    //取得指定學習單的最後一個學習任務
    public static function last($id)
    {
        return GameTask::where('worksheet_id', $id)->whereNull('next_task')->first();
    }

    //提供可報名年級中文字串
    public function getIsOrphanAttribute()
    {
        return !$this->prev && !$this->next;
    }

    public function worksheet()
    {
        return $this->hasOne('App\Models\GameWorksheet', 'id', 'worksheet_id');
    }

    //取得此任務的前一個任務
    public function prev()
    {
        return $this->hasOne('App\Models\GameTask', 'next_task', 'id');
    }

    //取得此任務的下一個任務
    public function next()
    {
        return $this->hasOne('App\Models\GameTask', 'id', 'next_task');
    }

}
