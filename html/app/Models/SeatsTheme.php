<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatsTheme extends Model
{

	protected $table = 'seats_theme';

    public static $styles = [
        0 => 'bg-white',
        1 => 'bg-gray-200',
        2 => 'bg-amber-300',
        3 => 'bg-lime-300',
        4 => 'bg-emerald-300',
        5 => 'bg-cyan-300',
        6 => 'bg-blue-300',
        7 => 'bg-violet-300',
        8 => 'bg-pink-300',
    ];

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'matrix', //使用json格式的二維陣列
        'uuid',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'creater',
        'seats',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'matrix' => 'array',
    ];

    //取得此座位表版型的建立者
    public function creater()
    {
        return $this->belongsTo('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此座位表版型已套用的所有座位表
    public function seats()
    {
        return $this->hasMany('App\Models\Seats', 'theme_id');
    }
}
