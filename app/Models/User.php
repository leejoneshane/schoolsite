<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Providers\TpedussoServiceProvider;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'idno',
        'user_type',
        'account',
        'sn',
        'gn',
        'name',
        'dept_id',
        'dept_name',
        'role_id',
        'role_name',
        'birthdate',
        'gender',
        'email',
        'mobile',
        'telephone',
        'address',
        'www',
        'class',
        'seat',
        'character',
        'status',
        'fetch_date',
        'password',
        'is_admin',
        'is_parent',
        'is_deleted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
        'is_parent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin' => 'boolean',
		'is_parent' => 'boolean',
        'is_deleted' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function gmails()
	{
    	return $this->hasMany('App\Models\Gsuite', 'uuid', 'uuid');
	}

	public function socialite_accounts()
	{
    	return $this->hasMany('App\Models\SocialiteAccount', 'uuid', 'uuid');
	}

    public function sync()
    {
        $sso = new SSO();
        // todo
    }
}
