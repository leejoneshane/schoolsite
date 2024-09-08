<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameFace extends Model
{

    protected $table = 'game_faces';

    //以下屬性可以批次寫入
    protected $fillable = [
        'file_name',
    ];

    public function path()
    {
        if ($this->avaliable) return public_path('images/faces/'.$this->file_name);
    }

    public function url()
    {
        if ($this->avaliable) return asset('images/faces/'.$this->file_name);
    }

    public function base64()
    {
        return base64_encode(file_get_contents($this->path()));
    }

    public function avaliable()
    {
        return file_exists($this->path());
    }

}
