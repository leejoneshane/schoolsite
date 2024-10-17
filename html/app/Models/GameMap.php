<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMap extends Model
{

    protected $table = 'game_maps';

    //以下屬性可以批次寫入
    protected $fillable = [
        'map',
    ];

    //更新角色時，自動進行升級
    protected static function booted()
    {
        self::deleting(function($item)
        {
            if ($item->avaliable()) {
                unlink($item->path());
            }
        });
    }

    public function path()
    {
        return public_path($this->map);
    }

    public function url()
    {
        return asset($this->map);
    }

    public function base64()
    {
        return base64_encode(file_get_contents($this->path()));
    }

    public function avaliable()
    {
        return $this->map && file_exists($this->path());
    }

}
