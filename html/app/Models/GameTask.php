<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameTask extends Model
{

    protected $table = 'game_tasks';

    //以下屬性可以批次寫入
    protected $fillable = [
        'worksheet_id', //學習單編號
        'sequence',     //順序
        'coordinate_x', //X座標
        'coordinate_y', //Y座標
        'story',        //故事
        'task',         //學習任務
        'review',       //是否需要人工審核
        'reward_xp',    //完成後的經驗值獎勵
        'reward_gp',    //完成後的金幣獎勵
        'reward_item',  //完成後的道具獎勵
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'worksheet',
    ];

    //篩選指定學習單的所有學習任務
    public static function findByWorksheet($id)
    {
        return GameTask::where('worksheet_id', $id)->orderBy('seqence')->get();
    }

    public function worksheet()
    {
        return $this->hasOne('App\Models\GameWorksheet', 'id', 'worksheet_id');
    }

}
