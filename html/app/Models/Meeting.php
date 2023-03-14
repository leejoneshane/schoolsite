<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\Subscribeable;

class Meeting extends Model implements Subscribeable
{

    protected $table = 'meeting';
    const template = 'emails.meeting';

    //以下屬性可以批次寫入
    protected $fillable = [
        'unit_id',
        'role',
        'reporter',
        'words',
        'inside', //若為 true 則不會透過電子報派送
        'expired_at',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'unit',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'inside' => 'boolean',
        'expired_at' => 'datetime:Y-m-d',
    ];

    //取得要輸出到電子報的網路朝會報告
    public function newsletter()
    {
        $meets = Meeting::inTimeOpen(date('Y-m-d'));
        if ($meets) {
            return ['meets' => $meets];
        } else {
            return null;
        }
    }

    //取得輸入此網路朝會報告的單位
    public function unit()
    {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    //篩選指定日期的所有網路朝會報告，靜態函式
    public static function inTime($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return Meeting::whereDate('created_at', '<=', $dt)
            ->whereDate('expired_at', '>=', $dt)
            ->orderBy('unit_id')
            ->get();
    }

    //篩選指定日期的所有內部網路朝會報告，靜態函式
    public static function inTimeInside($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return Meeting::where('inside', true)
            ->whereDate('created_at', '<=', $dt)
            ->whereDate('expired_at', '>=', $dt)
            ->orderBy('unit_id')
            ->get();
    }

    //篩選指定日期的所有公開網路朝會報告，靜態函式
    public static function inTimeOpen($date)
    {
        if (is_string($date)) {
            $dt = $date;
        } else {
            $dt = $date->toDateString();
        }
        return Meeting::where('inside', false)
            ->whereDate('created_at', '<=', $dt)
            ->whereDate('expired_at', '>=', $dt)
            ->orderBy('unit_id')
            ->get();
    }

}