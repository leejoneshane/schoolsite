<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingContext extends Model
{

	protected $table = 'writing_context';

    //以下屬性可以批次寫入
    protected $fillable = [
        'genre_id',
        'uuid',
        'title',
        'words',
        'author',
        'classname',
        'hits',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'genre',
    ];

    //取得此分類的所有報修紀錄
    public function genre()
    {
        return $this->belongsTo('App\Models\WritingGenre', 'genre_id');
    }

}
