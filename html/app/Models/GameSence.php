<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameSence extends Model
{

    protected $table = 'game_sences';
    protected $primaryKey = 'classroom_id';
    public $incrementing = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id',
        'uuid',
    ];

    //檢查指定班級是否鎖定，靜態函式
    public static function is_lock($classroom_id)
    {
        GameSence::where('ended_at', '<', Carbon::now())->delete();
        return GameSence::where('classroom_id', $classroom_id)->exists();
    }

    //鎖定指定班級，靜態函式
    public static function lock($classroom_id, $uuid)
    {
        GameSence::where('ended_at', '<', Carbon::now())->delete();
        $endtime = Carbon::now()->addMinutes(40);
        if (GameSence::is_lock($classroom_id)) return LOCK_ALREADY;
        GameSence::insert([
            'classroom_id' => $classroom_id,
            'uuid' => $uuid,
            'ended_at' => $endtime,
        ]);
    }

    //取得指定班級鎖定者，靜態函式
    public static function get_teacher($classroom_id)
    {
        GameSence::where('ended_at', '<', Carbon::now())->delete();
        if (GameSence::is_lock($classroom_id)) {
            return GameSence::find($classroom_id)->teacher;
        } else {
            return null;
        }
    }

    //取得鎖定此遊戲的教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
