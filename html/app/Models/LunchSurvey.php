<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LunchSurvey extends Model
{

	protected $table = 'lunch_survey';

    //以下屬性可以批次寫入
    protected $fillable = [
        'section',
        'uuid',
        'class_id',
        'seat',
        'by_school',
        'vegen',
        'milk',
        'by_parent',
        'boxed_meal',
        'memo',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'lunch_type',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'by_school' => 'boolean',
        'vegen' => 'boolean',
        'milk' => 'boolean',
        'by_parent' => 'boolean',
        'boxed_meal' => 'boolean',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'classroom',
        'student',
    ];

    //建立午餐調查表物件模型時，若省略學期，則預設為目前學期
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->section)) {
                $model->section = next_section();
            }
        });
    }

    //根據 uuid 和學期篩選午餐調查表，靜態函式
    public static function findBy($uuid, $section = null)
    {
        if (!$section) $section = next_section();
        return LunchSurvey::where('uuid', $uuid)->where('section', $section)->first();
    }

    //取得午餐調查設定，傳回物件，靜態函式
    public static function settings($section = null)
    {
        if (!$section) $section = next_section();
        return DB::table('lunch')->where('section', $section)->first();
    }

    //取得最後一次午餐調查設定，傳回物件，靜態函式
    public static function latest_settings()
    {
        return DB::table('lunch')->orderBy('survey_at', 'desc')->first();
    }

    //取得已調查的所有學期，傳回陣列，靜態函式
    public static function sections()
    {
        return DB::table('lunch_survey')->selectRaw('DISTINCT(section)')->get()->transform(function ($item) {
            return $item->section;
        })->toArray();
    }

    //篩選指定學期所有學生的午餐調查表，靜態函式
    public static function section_survey($section = null)
    {
        if (!$section) $section = next_section();
        return LunchSurvey::where('section', $section)->orderBy('class_id')->orderBy('seat')->get();
    }

    //篩選指定班級所有學生的午餐調查表，靜態函式
    public static function class_survey($class, $section = null)
    {
        if (!$section) $section = next_section();
        return LunchSurvey::where('section', $section)->where('class_id', $class)->orderBy('seat')->get();
    }

    //計算本學期已調查班級數
    public static function count_classes($section = null)
    {
        if (!$section) $section = next_section();
        return LunchSurvey::query()->distinct('class_id')->where('section', $section)->count();
    }

    //檢查本學期已調查學生數
    public static function count($section = null)
    {
        if (!$section) $section = next_section();
        return LunchSurvey::query()->where('section', $section)->count();
    }

    //提供午餐類型中文字串
    public function getLunchtypeAttribute()
    {
        $str = '';
        if ($this->by_school) {
            if ($this->vegen) {
                $str = '素食、';
            } else {
                $str = '葷食、';
            }
            if ($this->milk) {
                $str .= '可食用牛奶！';
            } else {
                $str .= '以豆乳取代牛奶！';
            }
        }
        return $str;
    }

    //取得填寫此午餐調查的學生
    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'uuid', 'uuid');
    }

    //取得填寫此午餐調查的班級
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'id', 'class_id');
    }

}
