<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubSection extends Model
{

	protected $table = 'clubs_section';

    protected static $weekMap = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];

    //以下屬性可以批次寫入
    protected $fillable = [
        'section',
        'club_id',
        'weekdays',
        'self_defined',
        'startDate',
        'endDate',
        'startTime',
        'endTime',
        'teacher',
        'location',
        'memo',
        'cash',
        'total',
        'maximum',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'club',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'weekdays' => 'array',
        'self_defined' => 'boolean',
        'startDate' => 'datetime:Y-m-d',
        'endDate' => 'datetime:Y-m-d',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'name',
        'studytime',
    ];

    //提供學期中文字串
    public function getNameAttribute()
    {
        $seme = substr($this->section, -1);
        if ($seme == 1) {
            $strseme = '上學期';
        } else {
            $strseme = '下學期';
        }
        return '第'.substr($this->section, 0, -1).'學年'.$strseme;
    }

    //提供上課時間中文字串
    public function getStudytimeAttribute()
    {
        $str ='';
        $str .= substr($this->startDate, 0, 10);
        $str .= '～';
        $str .= substr($this->endDate, 0, 10);
        if ($this->self_defined) {
            $str .= ' 每週上課日由家長自選';
        } else {
            $str .= ' 每週';
            foreach ($this->weekdays as $d) {
                $str .= self::$weekMap[$d];
            }
        }
        $str .= ' ';
        $str .= $this->startTime;
        $str .= '～';
        $str .= $this->endTime;
        return $str;
    }

    //取得此開班訊息所屬社團
    public function club()
    {
        return $this->belongsTo('App\Models\Club');
    }

}
