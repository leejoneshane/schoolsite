<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingGenre extends Model
{

	protected $table = 'writing_genre';
    public $timestamps = false;

    //以下屬性可以批次寫入
    protected $fillable = [
        'name',
        'description',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'contexts',
    ];

    //取得此專欄的所有投稿作品
    public function contexts()
    {
        return $this->hasMany('App\Models\WritingContext', 'genre_id');
    }
}
