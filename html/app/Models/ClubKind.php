<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClubKind extends Model
{

	protected $table = 'club_kinds';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'single',
        'stop_enroll',
        'manual_auditing',
        'enrollDate',
        'expireDate',
        'workTime',
        'restTime',
        'style',
        'weight',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'clubs',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'single' => 'boolean',
        'stop_enroll' => 'boolean',
        'manual_auditing' => 'boolean',
        'enrollDate' => 'datetime:Y-m-d',
        'expireDate' => 'datetime:Y-m-d',
    ];

    //篩選已開放報名的所有社團分類，靜態函式
    public static function can_enroll()
    {
        $today = Carbon::now();
        return ClubKind::where('stop_enroll', false)
            ->where('enrollDate', '<=', $today)
            ->where('expireDate', '>=', $today)
            ->get();
    }

    //取得此社團分類的所有社團
    public function clubs()
    {
        return $this->hasMany('App\Models\Club', 'kind_id');
    }

    //取得此社團分類目前開放報名的所有社團
    public function enroll_clubs()
    {
        return $this->clubs()->where('stop_enroll', false)->get();
    }

}
