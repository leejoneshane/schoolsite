<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gsuite extends Model
{
    protected $table = 'gsuite';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
  
    protected $fillable = [
        'uuid', 'userKey', 'primary',
    ];
    
    protected $casts = [
        'primary' => 'boolean',
    ];
  
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'uuid', 'uuid');
    }
}