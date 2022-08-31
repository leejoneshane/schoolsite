<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Permission;

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
        'user_type',
        'name',
        'email',
        'password',
        'is_admin',
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
    ];

    protected $appends = [
		'profile',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public static function admins()
    {
        return User::where('is_admin', true)->get();
    }

	public function socialite_accounts()
	{
    	return $this->hasMany('App\Models\SocialiteAccount', 'uuid', 'uuid');
	}

    public function permissions()
	{
    	return $this->belongsToMany('App\Models\Permissions', 'user_permission', 'uuid', 'perm_id');
	}

    public function getProfileAttribute()
    {
        if ($this->user_type == 'Teacher') {
            return Teacher::find($this->uuid)->getAttributes();
        }
        if ($this->user_type == 'Student') {
            return Student::find($this->uuid)->getAttributes();
        }
        return [];
    }

    public function givePermission($permission)
    {
        $perm = Permission::findByName($permission);
        $perm->assignByUUID($this->uuid);
    }

    public function takePermission($permission)
    {
        $perm = Permission::findByName($permission);
        $perm->removeByUUID($this->uuid);
    }

    public function hasPermission($permission)
    {
        $perm = Permission::findByName($permission);
        return $perm->checkByUUID($this->uuid);
    }

}
