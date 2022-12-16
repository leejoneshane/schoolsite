<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Seniority extends Model
{

    protected $table = 'seniority';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'syear',
        'no',
        'school_year',
        'school_month',
        'school_score',
        'teach_year',
        'teach_month',
        'teach_score',
        'ok',
        'new_school_year',
        'new_school_month',
        'new_school_score',
        'new_teach_year',
        'new_teach_month',
        'new_teach_score',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'years',
        'score',
        'newyears',
        'newscore',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'ok' => 'boolean',
        'school_year' => 'integer',
        'school_month' => 'integer',
        'school_score' => 'float',
        'teach_year' => 'integer',
        'teach_month' => 'integer',
        'teach_score' => 'float',
        'new_school_year' => 'integer',
        'new_school_month' => 'integer',
        'new_school_score' => 'float',
        'new_teach_year' => 'integer',
        'new_teach_month' => 'integer',
        'new_teach_score' => 'float',
    ];

    //建立年資積分物件模型時，若省略學年，則預設為目前學年
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = current_year();
            }
        });
    }

    //提供教學年資（在校年資 + 校外年資）
    public function getYearsAttribute()
    {
        return round(($this->school_year * 12 + $this->teach_year * 12 + $this->school_month + $this->teach_month) / 12, 2);
    }

    //提供總積分（在校積分 + 校外積分）
    public function getScoreAttribute()
    {
        return $this->school_score + $this->teach_score;
    }

    //提供修正後的教學年資
    public function getNewyearsAttribute()
    {
        return round(($this->new_school_year * 12 + $this->new_teach_year * 12 + $this->new_school_month + $this->new_teach_month) / 12, 2);
    }

    //提供修正後的總積分
    public function getNewscoreAttribute()
    {
        return $this->new_school_score + $this->new_teach_score;
    }

    //根據 uuid 和學年篩選年資積分物件，靜態函式
    public static function findBy($uuid, $syear)
    {
        return Seniority::where('uuid', $uuid)->where('syear', $syear)->first();
    }

    //取得有年資統計的所有學年，傳回陣列，靜態函式
    public static function years()
    {
        return DB::table('seniority')->select(['syear'])->distinct()->get()->map(function ($item) {
            return $item->syear;
        })->toArray();
    }

    //篩選當前學年度所有的年資積分物件，靜態函式
    public static function current()
    {
        return Seniority::where('syear', current_year())->orderBy('no')->get();
    }

    //取得此年資積分的擁有教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
