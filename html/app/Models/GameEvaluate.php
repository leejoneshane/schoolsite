<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameEvaluate extends Model
{

    protected $table = 'game_evaluates';

    //以下屬性可以批次寫入
    protected $fillable = [
        'title',       //評量名稱
        'subject',     //科目名稱
        'range',       //評量範圍
        'grade_id',    //適用年級
        'uuid',        //出題者
        'share',       //共享
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'grade',
        'teacher',
        'questions',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'teacher_name',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'share' => 'boolean',
    ];

    //自動移除所有題目
    protected static function booted()
    {
        self::deleting(function($item)
        {
            foreach ($item->questions as $q) {
                $q->delete();
            }
        });
    }

    //提供此評量的出題教師姓名
    public function getTeacherNameAttribute()
    {
        return $this->teacher->realname;
    }

    //篩選指定的出題者的所有試卷
    public static function findByUuid($uuid)
    {
        return GameEvaluate::where('uuid', $uuid)
            ->orWhere('share', 1)
            ->orderBy('grade_id')
            ->get();
    }

    //取得此評量的出題教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此評量的適用年級
    public function grade()
    {
        return $this->hasOne('App\Models\Grade', 'id', 'grade_id');
    }

    //取得此評量的所有題目，依序排列
    public function questions()
    {
        return $this->hasMany('App\Models\GameQuestion', 'evaluate_id')->orderBy('sequence');
    }

    //取得此評量的最後題目的編號
    public function max()
    {
        return $this->hasMany('App\Models\GameQuestion', 'evaluate_id')->latest('sequence')->first();
    }

    //隨機出題
    public function random()
    {
        $questions = $this->questions->shuffle();
        foreach ($questions as $q) {
            $q->selection = $q->random();
        }
        return $questions;
    }

}
