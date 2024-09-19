<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\GameCharacter;
use Carbon\Carbon;

class GameDelay extends Model
{

    protected $table = 'game_delays';

    //以下屬性可以批次寫入
    protected $fillable = [
        'classroom_id', //班級
        'uuid',         //教師 uuid
        'characters',   //受處罰學生的 uuid (Json 格式)
        'rule',         //違反條款的內容
        'reason',       //臨時條款的內容
        'hp',           //應扣健康值
        'mp',           //應扣行動力
        'act',          //是否已執行
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'act' => 'boolean',
        'characters' => 'array',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'description',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'description',
        'classroom',
        'teacher',
    ];

    //提供此延遲處置的文字描述
    public function getDescriptionAttribute()
    {
        $students = [];
        foreach ($this->students() as $stu) {
            $students[] = $stu->seat.' '.$stu->realname;
        }
        $message = implode('、', $students);
        $rule = GameSetting::find($this->rule);
        $message .= '因為'.$rule->description.'受到天罰，損失';
        if ($this->hp > 0) {
            $add[] = '生命力' . $this->hp . '點';
        }
        if ($this->mp > 0) {
            $add[] = '法力（行動力）' . $this->mp . '點';
        }
        $message .= implode('、', $add).'。';
        return $message;
    }

    //取得指定日延遲處置紀錄，靜態函式
    public static function delayByDate($room_id, $date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        } elseif (is_string($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }
        return GameDelay::where('classroom_id', $room_id)
            ->whereRaw('DATE(created_at) = ?', $date->format('Y-m-d'))
            ->where('act', 0)
            ->get();
    }

    //搜尋指定教師的瀏覽紀錄，靜態函式
    public static function delayByTeacher($room_id, $uuid = null)
    {
        if (!$uuid) {
            $uuid = auth()->user()->uuid;
        }
        return GameDelay::where('classroom_id', $room_id)
            ->where('uuid', $uuid)
            ->where('act', 0)
            ->get();
    }

    //取得此紀錄所屬班級
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    //取得產生此紀錄的教師
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    //取得產生此紀錄的學生
    public function students()
    {
        return Student::whereIn('uuid', $this->characters)->get();
    }

    //取得產生此紀錄的學生
    public function asCharacters()
    {
        return GameCharacter::whereIn('uuid', $this->characters)->get();
    }

}
