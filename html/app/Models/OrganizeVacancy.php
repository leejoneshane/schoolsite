<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrganizeSurvey;

class OrganizeVacancy extends Model
{

    protected $table = 'organize_vacancy';

    //以下屬性可以批次寫入
    protected $fillable = [
        'id',
        'syear',
        'type', //職缺類型分為：'admin'（行政）, 'tutor'（導師）, 'domain'（領域教師）
        'role_id',
        'grade_id',
        'domain_id',
        'special',
        'name',
        'stage', //意願調查分為兩個階段
        'shortfall',
        'filled',
        'assigned',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'role',
        'grade',
        'domain',
        'original',
        'reserved',
        'assigned',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'special' => 'boolean',
    ];

    //建立職缺物件模型時，若省略學年，則預設為目前學年
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            if (empty($model->syear)) {
                $model->syear = current_year();
            }
        });
    }

    //篩選指定學年的所有職缺，靜態函式
    public static function year($year = null)
    {
        if (!$year) $year = current_year();
        return OrganizeVacancy::where('syear', $year)->get();
    }

    //篩選指定學年、指定類型的所有職缺，靜態函式
    public static function year_type($type, $year = null)
    {
        if (!$year) $year = current_year();
        return OrganizeVacancy::where('syear', $year)->where('type', $type)->get();
    }

    //篩選指定學年、指定階段的所有職缺，靜態函式
    public static function year_stage($stage, $year = null)
    {
        if (!$year) $year = current_year();
        $general = OrganizeVacancy::where('syear', $year)->where('stage', $stage)->where('special', false)->get();
        $special = OrganizeVacancy::where('syear', $year)->where('stage', $stage)->where('special', true)->get();
        return (object) array('general' => $general, 'special' => $special);
    }

    //計算職編完成率，靜態函式
    public static function completeness()
    {
        $shortfall = OrganizeVacancy::where('syear', current_year())->sum('shortfall');
        $reserved = OrganizeVacancy::where('syear', current_year())->sum('filled');
        $assigned = OrganizeVacancy::where('syear', current_year())->sum('assigned');
        $completeness = intval($assigned / ($shortfall - $reserved) * 100);
        return (object) ['shortfall' => $shortfall, 'reserved' => $reserved, 'assigned' => $assigned, 'completeness' => $completeness];
    }

    //取得此職缺的職務物件
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    //取得此職缺的年級物件
    public function grade()
    {
        return $this->belongsTo('App\Models\Grade');
    }

    //取得此職缺的領域物件
    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }

    //取得此職缺的任職教師
    public function original()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_original', 'vacancy_id', 'uuid')->where('syear', $this->syear);
    }

    //取得此職缺的保留職缺教師
    public function reserved()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_reserved', 'vacancy_id', 'uuid')->where('syear', $this->syear);
    }

    //取得已編排擔任此職缺的教師
    public function assign()
    {
        return $this->belongsToMany('App\Models\Teacher', 'organize_assign', 'vacancy_id', 'uuid')->where('syear', $this->syear);
    }

    //根據指定的志願序，計算此職缺的選填人數
    public function count_survey($field = null)
    {
        if ($this->special) {
            return OrganizeSurvey::where('syear', $this->syear)
                ->whereJsonContains('special', $this->id)
                ->count();
        } elseif (is_array($field)) {
            return OrganizeSurvey::where('syear', $this->syear)->where(function ($query) use ($field) {
                foreach ($field as $k => $f) {
                    if ($k == 0) {
                        $query->where($f, $this->id);    
                    } else {
                        $query->orWhere($f, $this->id);
                    }
                }
            })->count();
        } elseif ($field) {
            return OrganizeSurvey::where('syear', $this->syear)
                ->where($field, $this->id)
                ->count();
        }
    }

}
