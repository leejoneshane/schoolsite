<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameImage extends Model
{

    protected $table = 'game_images';

    //以下屬性可以批次寫入
    protected $fillable = [
        'owner_id',
        'owner_type',
        'picture',
        'thumbnail',
    ];

    //自動移除實體檔案
    protected static function booted()
    {
        self::deleting(function($item)
        {
            if ($item->avaliable()) {
                unlink($item->path());
            }
            if ($item->thumb_avaliable()) {
                unlink($item->thumb_path());
            }
        });
    }

    //取得此 Image 物件的擁有者（角色或怪物）
    public function owner()
    {
        return $this->morphTo();
    }

    //篩選指定職業的圖片，靜態函式
    public static function forClass($class_id)
    {
        return GameImage::where('owner_type', 'App\Models\GameClass')
            ->where('owner_id', $class_id)
            ->get();
    }

    //篩選指定怪物的圖片，靜態函式
    public static function forMonster($monster_id)
    {
        return GameImage::where('owner_type', 'App\Models\GameMonster')
            ->where('owner_id', $monster_id)
            ->get();
    }

    public function path()
    {
        return public_path($this->picture);
    }

    public function thumb_path()
    {
        return public_path($this->thumbnail);
    }

    public function url()
    {
        return asset($this->picture);
    }

    public function thumb_url()
    {
        return asset($this->thumbnail);
    }

    public function base64()
    {
        return base64_encode(file_get_contents($this->path()));
    }

    public function thumb_base64()
    {
        return base64_encode(file_get_contents($this->thumb_path()));
    }

    public function avaliable()
    {
        return $this->picture && file_exists($this->path());
    }

    public function thumb_avaliable()
    {
        return $this->thumbnail && file_exists($this->thumb_path());
    }

}
