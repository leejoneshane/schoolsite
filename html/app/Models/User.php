<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Permission;

class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * HasApiTokens: 可透過 API 驗證
     * HasFactory: 可自動產生測試資料
     * Notifiable: 可以接收通知
     */
    use HasApiTokens, HasFactory, Notifiable;

    //以下屬性可以批次寫入
    protected $fillable = [
        'uuid',
        'user_type',
        'name',
        'email',
        'password',
        'is_admin',
    ];

    //以下屬性隱藏不顯示（render 時忽略）
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    //以下為透過程式動態產生之屬性
    protected $appends = [
        'profile',
    ];

    //以下屬性需進行資料庫欄位格式轉換
    protected $casts = [
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    //取得所有管理員的使用者物件，靜態函式
    public static function admins()
    {
        return User::where('is_admin', true)->get();
    }

    //取得使用者的所有社交帳戶
    public function socialite_accounts()
    {
        return $this->hasMany('App\Models\SocialiteAccount', 'uuid', 'uuid');
    }

    //取得使用者的所有權限
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permissions', 'user_permission', 'uuid', 'perm_id');
    }

    //提供使用者的真實身份物件，可能為教師物件或學生物件
    public function getProfileAttribute()
    {
        if ($this->user_type == 'Teacher') {
            return Teacher::find($this->uuid);
        }
        if ($this->user_type == 'Student') {
            return Student::find($this->uuid);
        }
        return (object) array('realname' => '管理員');
    }

    //授予使用者權限
    public function givePermission($permission)
    {
        $perm = Permission::findByName($permission);
        if ($perm) $perm->assign($this->uuid);
    }

    //移除使用者權限
    public function takePermission($permission)
    {
        $perm = Permission::findByName($permission);
        if ($perm) $perm->remove($this->uuid);
    }

    //檢查使用者是否被授權
    public function hasPermission($permission)
    {
        $perm = Permission::findByName($permission);
        if ($perm) return $perm->check($this->uuid);
        return false;
    }

}
