<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

	protected $table = 'menus';
    public $incrementing = false;
    protected $keyType = 'string';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'parent_id',
        'caption',
        'url',
    ];

    public function childs() {
        return $this->hasMany('App\Models\Menu','parent_id','id') ;
    }

}
