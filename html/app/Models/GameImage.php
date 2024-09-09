<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameImage extends Model
{

    protected $table = 'game_images';

    //以下屬性可以批次寫入
    protected $fillable = [
        'file_name',
        'thumbnail',
    ];

    //以下屬性隱藏不顯示（toJson 時忽略）
    protected $hidden = [
        'profession',
    ];

    //取得此圖片的職業
    public function profession()
    {
        return $this->belongsToMany('App\Models\GameClass', 'game_classes_images', 'image_id', 'class_id')->orderByPivot('image_id');
    }

    //篩選指定職業的圖片，靜態函式
    public static function forClass($class_id)
    {
        return GameImage::select('game_images.*')
            ->leftjoin('game_classes_images', 'game_images.id', '=', 'game_classes_images.image_id')
            ->where('game_classes_images.class_id', $class_id)
            ->orderBy('game_images.id')
            ->get();
    }

    public function path()
    {
        if ($this->avaliable) return public_path(GAME_CHARACTER.$this->file_name);
    }

    public function thumb_path()
    {
        if ($this->avaliable) return public_path(GAME_FACE.$this->file_name);
    }

    public function url()
    {
        if ($this->avaliable) return asset(GAME_CHARACTER.$this->file_name);
    }

    public function thumb_url()
    {
        if ($this->avaliable) return asset(GAME_FACE.$this->file_name);
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
        return file_exists($this->path());
    }

    public function thumb_avaliable()
    {
        return file_exists($this->thumb_path());
    }

}
