<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{

	protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group',
        'permission',
    ];

    public function delete()
    {
        DB::table('user_permissions')->where('perm_id', self::$id)->delete();
        return parent::delete();
    }

    public function findByName($permissions)
    {
        list($group, $permission) = explode('.', $permissions);
        return DB::table('permissions')
            ->where('group', $group)
            ->where('permission', $permission)
            ->first();
    }

    public function check($user)
    {
        if (!($users instanceof User)) return false;
        $checked = DB::table('user_permissions')
            ->where('perm_id', self::$id)
            ->where('uuid', $user->uuid)
            ->first();
        if ($checked) return true;
        return false;
    }

    public function checkByUUID($uuid)
    {
        if (empty($uuid)) return false;
        $checked = DB::table('user_permissions')
            ->where('perm_id', self::$id)
            ->where('uuid', $uuid)
            ->first();
        if ($checked) return true;
        return false;
    }

    public function assign($users)
    {
        if ($users instanceof User) {
            $user = array([
                'uuid' => $users->uuid,
                'perm_id' => self::$id,
            ]);
            $user_permissions[] = $user;
        } elseif ($users instanceof Collection) {
            foreach ($users as $user) {
                $user = array([
                    'uuid' => $user->uuid,
                    'perm_id' => self::$id,    
                ]);
                $user_permissions[] = $user;    
            }
        }
        foreach ($users as $uuid) {
            DB::table('user_permissions')->updateOrInsert($user_permissions);    
        }
        return $this;
    }

    public function assignByUUID($uuids)
    {
        $user_permissions = [];
        if (!is_array($uuids)) {
            $user = array([
                'uuid' => $uuids,
                'perm_id' => self::$id,
            ]);
            $user_permissions[] = $user;
        } else {
            foreach ($uuids as $uuid) {
                $user = array([
                    'uuid' => $uuid,
                    'perm_id' => self::$id,    
                ]);
                $user_permissions[] = $user;    
            }
        }
        foreach ($users as $uuid) {
            DB::table('user_permissions')->updateOrInsert($user_permissions);    
        }
        return $this;
    }


    public function remove($users)
    {
        $uuids = [];
        if ($users instanceof User) {
            $uuids[] = $users->uuid;
        } elseif ($users instanceof Collection) {
            foreach ($users as $user) {
                $uuids[] = $user->uuid;
            }
        }
        DB::table('user_permissions')->where('perm_id', self::$id)->whereIn('uuid', $uuids)->delete();
        return $this;
    }

    public function removeByUUID($uuids)
    {
        if (!is_array($uuids)) {
            $uuids = array($uuids);
        }
        DB::table('user_permissions')->where('perm_id', self::$id)->whereIn('uuid', $uuids)->delete();
        return $this;
    }

}
