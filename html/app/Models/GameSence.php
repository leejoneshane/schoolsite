<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        return LOCKED;
    }

    //鎖定指定班級，靜態函式
    public static function unlock($classroom_id, $uuid)
    {
        GameSence::where('ended_at', '<', Carbon::now())->delete();
        GameSence::where('classroom_id', $classroom_id)->where('uuid', $uuid)->delete();
        return UNLOCKED;
    }

    //取得指定班級鎖定者，靜態函式
    public static function lockBy($room_id)
    {
        GameSence::where('ended_at', '<', Carbon::now())->delete();
        if (GameSence::is_lock($room_id)) {
            return GameSence::find($room_id)->teacher;
        } else {
            return null;
        }
    }

    //取得指定班級鎖定者，靜態函式
    public static function lockByMe($room_id)
    {
        GameSence::where('ended_at', '<', Carbon::now())->delete();
        return GameSence::where('classroom_id', $room_id)
            ->where('uuid', Auth::user()->uuid)
            ->exists();
    }

    //取得鎖定此遊戲的教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
