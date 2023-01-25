<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Roster extends Model
{

	protected $table = 'rosters';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'grades',
        'fields',
        'domains',
        'started_at',
        'ended_at',
        'min',
        'max',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'students',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'grades' => 'array',
        'fields' => 'array',
        'domains' => 'array',
        'started_at' => 'datetime:Y-m-d',
        'ended_at' => 'datetime:Y-m-d',
    ];

    //取得已填報的所有學期，傳回陣列，靜態函式
    public static function sections()
    {
        return DB::table('rosters_students')->selectRaw('DISTINCT(section)')->get()->transform(function ($item) {
            return $item->section;
        })->toArray();
    }

    //取得已填報學生集合
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'rosters_students', 'roster_id', 'uuid')
            ->as('list')
            ->withPivot([
                'id',
                'section',
                'class_id',
                'deal',
                'created_at',
                'updated_at',
            ]);
    }

    //取得指定學年或本學年之學生名單
    public function year_students($section = null)
    {
        if ($section) {
            return $this->students()->wherePivot('section', $section)->get();
        } else {
            return $this->students()->wherePivot('section', current_section())->get();
        }
    }

    //計算本學期已填報班級數
    public function count_classes($section = null)
    {
        if ($section) {
            return DB::table('rosters_students')->distinct('class_id')->where('section', $section)->count();
        } else {
            return DB::table('rosters_students')->distinct('class_id')->where('section', current_section())->count();
        }
    }

    //檢查本學期已填報學生數
    public function count($section = null)
    {
        if ($section) {
            return DB::table('rosters_students')->where('section', $section)->count();
        } else {
            return DB::table('rosters_students')->where('section', current_section())->count();
        }
    }

    //檢查學生名單是否可以填報
    public function opened()
    {
        return Carbon::now() >= $this->started_at && Carbon::now() <= $this->ended_at;
    }

    //檢查學生名單是否不能填報
    public function closed()
    {
        return Carbon::now() < $this->started_at || Carbon::now() > $this->ended_at;
    }

}
