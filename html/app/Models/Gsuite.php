<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gsuite extends Model
{
    protected $table = 'gsuite';
  
    protected $fillable = [
        'owner_id', 'owner_type', 'userKey', 'primary',
    ];
    
    protected $casts = [
        'primary' => 'boolean',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

}