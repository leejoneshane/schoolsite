<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairKind extends Model
{

	protected $table = 'repair_kinds';

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'description',
        'manager',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'jobs',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'manager' => AsCollection::class,
    ];

    //檢查指定人員是否為管理員
    public function is_manager($uuid)
    {
        return $this->manager->contains($uuid);
    }

    //取得此分類的所有報修紀錄
    public function jobs()
    {
        return $this->hasMany('App\Models\RepairJob', 'kind_id');
    }

}
