<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    //取得已填報學生集合
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'rosters_students', 'roster_id', 'uuid')
            ->as('list')
            ->withPivot([
                'id',
                'year',
                'deal',
                'created_at',
                'updated_at',
            ]);
    }

    //取得指定學年或本學年之學生名單
    public function year_students($year = null)
    {
        if ($year) {
            return $this->students()->wherePivot('year', $year)->get();
        } else {
            return $this->students()->wherePivot('year', current_year())->get();
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
