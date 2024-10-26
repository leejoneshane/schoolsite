<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameLog extends Model
{

    protected $table = 'game_logs';

    //以下屬性可以批次寫入
    protected $fillable = [
        'syear',
        'classroom_id', //班級
        'uuid',         //教師 uuid，若無可省略
        'party_id',     //公會 id，若無可省略
        'character_uuid', //學生 uuid，若無可省略
        'content',      //日誌內容，若無可省略
    ];

    //紀錄遊戲日誌
    public static function log(Array $data)
    {
        $log = GameLog::create([
            'syear' => current_year(),
            'classroom_id' => $data['classroom'],
        ]);
        if ($data['uuid']) $log->uuid = $data['uuid'];
        if ($data['party']) $log->party = $data['party'];
        if ($data['character']) $log->character = $data['character'];
        if ($data['content']) $log->content = $data['content'];
        $log->save();
        return $log;
    }

    //取得指定日期的瀏覽紀錄，靜態函式
    public static function findByDate($room_id, $date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } elseif (is_string($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        return GameLog::where('syear', current_year())
            ->where('classroom_id', $room_id)
            ->whereRaw('DATE(created_at) = ?', $date->format('Y-m-d'))
            ->get();
    }

    //搜尋指定教師的瀏覽紀錄，靜態函式
    public static function findByUuid($room_id, $uuid = null)
    {
        if (!$uuid) {
            $uuid = auth()->user()->uuid;
        }
        return GameLog::where('syear', current_year())
            ->where('classroom_id', $room_id)
            ->where('uuid', $uuid)
            ->get();
    }

    //取得此紀錄所屬班級
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom');
    }

}
