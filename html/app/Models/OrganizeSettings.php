<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrganizeSettings extends Model
{

    protected $table = 'organize_settings';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'syear',
        'survey_at',
        'first_stage',
        'pause_at',
        'second_stage',
        'close_at',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'survey_at' => 'datetime:Y-m-d',
        'first_stage' => 'datetime:Y-m-d',
        'pause_at' => 'datetime:Y-m-d',
        'second_stage' => 'datetime:Y-m-d',
        'close_at' => 'datetime:Y-m-d',
    ];

    //建立職編流程物件模型時，若省略學年，則預設為目前學年
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = current_year();
            }
        });
    }

    //篩選今年度的職編流程，靜態函式
    public static function current()
    {
        return OrganizeSettings::where('syear', current_year())->first();
    }

    //取得已安排職編流程的所有學年，傳回陣列，靜態函式
    public static function years()
    {
        return DB::table('organize_settings')->select(['syear'])->distinct()->get()->map(function ($item) {
            return $item->syear;
        })->toArray();
    }

    //檢查目前職編是否尚未開始
    public function notStart()
    {
        return Carbon::now() < $this->survey_at;
    }

    //檢查目前是否正在進行意願調查
    public function onPeriod()
    {
        return Carbon::now()->between($this->survey_at, $this->close_at);
    }

    //檢查目前是否為填寫學經歷階段
    public function onSurvey()
    {
        return Carbon::now()->between($this->survey_at, $this->first_stage);
    }

    //檢查目前是否為第一階段意願調查
    public function onFirstStage()
    {
        return Carbon::now()->between($this->first_stage, $this->pause_at);
    }

    //檢查目前是否第一階段意願調查已經結束
    public function onPause()
    {
        return Carbon::now()->between($this->pause_at, $this->second_stage);
    }

    //檢查目前是否為第二階段意願調查
    public function onSecondStage()
    {
        return Carbon::now()->between($this->second_stage, $this->close_at);
    }

    //檢查意願調查是否已經結束
    public function onFinish()
    {
        return Carbon::now() > $this->close_at;
    }

}
