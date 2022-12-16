<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{

    protected $table = 'permissions';

    //以下屬性可以批次寫入
    protected $fillable = [
        'group',
        'permission',
        'description',
    ];

    //移除此權限時，一併移除此權限的所有授權紀錄
    public function delete()
    {
        DB::table('user_permissions')->where('perm_id', $this->id)->delete();
        return parent::delete();
    }

    //取得此權限所有已授權使用者
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_permissions', 'perm_id', 'uuid');
    }

    //取得此權限所有已授權使用者其對應的真實身份
    public function teachers()
    {
        return $this->belongsToMany('App\Models\Teacher', 'user_permissions', 'perm_id', 'uuid');
    }

    //根據權限代碼篩選符合的權限，靜態函式
    public static function findByName($permissions)
    {
        list($group, $permission) = explode('.', $permissions);
        return Permission::where('group', $group)
            ->where('permission', $permission)
            ->first();
    }

    //檢查使用者是否已擁有此權限
    public function check($user)
    {
        if ($user instanceof User) {
            $uuid = $user->uuid;
        } else {
            $uuid = $user;
        }
        $checked = DB::table('user_permissions')
            ->where('perm_id', $this->id)
            ->where('uuid', $uuid)
            ->first();
        if ($checked) return true;
        return false;
    }

    //授予此權限給指定使用者
    public function assign($users)
    {
        $user_permissions = [];
        if (is_array($users) || $users instanceof Collection) {
            foreach ($users as $user) {
                if ($user instanceof User) {
                    $uuid = $user->uuid;
                }
                if (is_string($user)) {
                    $uuid = $user;
                }
                $user_permissions[] = [
                    'uuid' => $uuid,
                    'perm_id' => $this->id,
                ];
            }
        } elseif ($users instanceof User) {
            $user_permissions[] = [
                'uuid' => $users->uuid,
                'perm_id' => $this->id,    
            ];
        } elseif (is_string($users)) {
            $user_permissions[] = [
                'uuid' => $users,
                'perm_id' => $this->id,    
            ];
        }
        foreach ($user_permissions as $u) {
            DB::table('user_permissions')->updateOrInsert($u, $u);
        }   
        return $this;
    }

    //移除此權限的所有授權紀錄
    public function removeAll()
    {
        DB::table('user_permissions')->where('perm_id', $this->id)->delete();
        return $this;
    }

    //移除指定使用者此權限的授權紀錄
    public function remove($users)
    {
        $uuids = [];
        if (is_array($users) || $users instanceof Collection) {
            foreach ($users as $user) {
                if ($user instanceof User) {
                    $uuids[] = $user->uuid;
                }
                if (is_string($user)) {
                    $uuids[] = $user;
                }
            }
        } elseif ($users instanceof User) {
            $uuids[] = $users->uuid;
        } elseif (is_string($users)) {
            $uuids[] = $users;
        }
        DB::table('user_permissions')->where('perm_id', $this->id)->whereIn('uuid', $uuids)->delete();
        return $this;
    }

}
