<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizeSurvey extends Model
{

    protected $table = 'organize_survey';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'syear',
        'uuid',
        'realname',
        'age',
        'exprience',
        'edu_level',
        'edu_school',
        'edu_division',
        'score',
        'high',
        'admin1',
        'admin2',
        'admin3',
        'special',
        'teach1',
        'teach2',
        'teach3',
        'teach4',
        'teach5',
        'teach6',
        'grade',
        'overcome',
        'assign',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'teacher',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'special' => 'array',
    ];

    //建立意願調查表物件模型時，若省略學年，則預設為目前學年
    protected static function booted()
    {
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = current_year();
            }
        });
    }

    //篩選今年度所有意願調查表，靜態函式
    public static function current()
    {
        return OrganizeSurvey::where('syear', current_year())->get();
    }

    //篩選指定人員今年度的意願調查表，靜態函式
    public static function findByUUID($uuid)
    {
        return OrganizeSurvey::where('syear', current_year())->where('uuid', $uuid)->first();
    }

    //取得填寫此意願調查表的教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

}
