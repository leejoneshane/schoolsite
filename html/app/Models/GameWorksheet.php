<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameWorksheet extends Model
{

    protected $table = 'game_worksheets';

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',        //設計者
        'title',       //學習單名稱
        'subject',     //科目名稱
        'description', //內容說明
        'grade_id',    //適用年級
        'map_id',      //地圖編號
        'next_task',   //下一個任務的 id
        'intro',       //故事綱要
        'share',       //是否分享
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'grade',
        'teacher',
        'map',
        'tasks',
        'begin',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'share' => 'boolean',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'orphans',
        'teacher_name',
    ];

    //自動移除所有學習任務
    protected static function booted()
    {
        self::deleting(function($item)
        {
            foreach ($item->tasks as $t) {
                $t->delete();
            }
        });
    }

    //提供此評量的出題教師姓名
    public function getTeacherNameAttribute()
    {
        return $this->teacher->realname;
    }

    //篩選指定設計者的所有學習單
    public static function findByUuid($uuid)
    {
        return GameWorksheet::where('uuid', $uuid)
            ->orWhere('share', 1)
            ->orderBy('grade_id')
            ->get();
    }

    //提供尚未組織完成的任務集合
    public function getOrphansAttribute()
    {
        return $this->tasks->filter(function ($task) {
            return $task->is_orphan;
        });
    }

    //取得此學習單的適用年級
    public function grade()
    {
        return $this->hasOne('App\Models\Grade', 'id', 'grade_id');
    }

    //取得此學習單的設計教師
    public function teacher()
    {
        return $this->hasOne('App\Models\Teacher', 'uuid', 'uuid');
    }

    //取得此學習單所使用的地圖
    public function map()
    {
        return $this->hasOne('App\Models\GameMap', 'id', 'map_id');
    }

    //取得此學習單的所有任務
    public function tasks()
    {
        return $this->hasMany('App\Models\GameTask', 'worksheet_id');
    }

    //取得此學習單的第一個任務
    public function begin()
    {
        return $this->hasOne('App\Models\GameTask', 'id', 'next_task');
    }

}
