<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialiteAccount extends Model
{
    protected $table = 'socialite_account';
    protected $primaryKey = 'uuid';
	public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid', 'socialite', 'userID',
    ];
    
	public function user()
	{
    	return $this->belongsTo('App\Models\User', 'uuid', 'uuid');
	}
}