<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameLog extends Model
{

    protected $table = 'game_logs';

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id', //班級
        'uuid',         //教師 uuid，若無可省略
        'party',        //公會 id，若無可省略
        'character',    //學生 uuid，若無可省略
        'reason',       //原因（規則、條款、狀態），若無可省略
        'action',       //動作（獎勵、懲罰、購買、出售、使用、施展、挑戰地下城、地圖冒險）
        'object',       //對象（學生姓名、公會名稱、地下城名稱、地圖名稱），若無可省略
        'content',      //內容（家具名稱、道具名稱、技能名稱），若無可省略
        'result',       //結果（屬性變化、狀態變化、金幣變化、道具變化），若無可省略
    ];

    //紀錄遊戲日誌
    public static function log(Array $data)
    {
        switch ($data['action']) {
            case '獎勵':
            case '懲罰':
                    $log = GameLog::create([
                    'classroom_id' => $data['classroom'],
                    'uuid' => $data['uuid'],
                    'reason' => GameSetting::find($data['rule'])->description,
                    'action' => $data['action'],
                    'object' => GameCharacter::find($data['object'])->name,
                    'result' => $data['result'],
                ]);
                break;
            case '購買家具':
            case '出售家具':
                $log = GameLog::create([
                    'classroom_id' => $data['classroom'],
                    'party' => $data['party'],
                    'action' => $data['action'],
                    'content' => GameFurniture::find($data['content'])->name,
                    'result' => $data['result'],
                ]);
                break;
            case 'buy':
                break;
        }
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
        return GameLog::where('classroom_id', $room_id)
            ->whereRaw('DATE(created_at) = ?', $date->format('Y-m-d'))
            ->latest()
            ->get();
    }

    //搜尋指定教師的瀏覽紀錄，靜態函式
    public static function findByUuid($room_id, $uuid = null)
    {
        if (!$uuid) {
            $uuid = auth()->user()->uuid;
        }
        return GameLog::where('classroom_id', $room_id)
            ->where('uuid', $uuid)
            ->latest()
            ->get();
    }

    //取得產生此紀錄的帳號
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom');
    }

}
