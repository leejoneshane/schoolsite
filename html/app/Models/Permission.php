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
        'description',
    ];

    public function delete()
    {
        DB::table('user_permissions')->where('perm_id', $this->id)->delete();
        return parent::delete();
    }

    public function users()
	{
    	return $this->belongsToMany('App\Models\User', 'user_permissions', 'perm_id', 'uuid');
	}

    public function teachers()
	{
    	return $this->belongsToMany('App\Models\Teacher', 'user_permissions', 'perm_id', 'uuid');
	}

    public static function findByName($permissions)
    {
        list($group, $permission) = explode('.', $permissions);
        return Permission::where('group', $group)
            ->where('permission', $permission)
            ->first();
    }

    public function check($user)
    {
        if (!($user instanceof User)) return false;
        $checked = DB::table('user_permissions')
            ->where('perm_id', $this->id)
            ->where('uuid', $user->uuid)
            ->first();
        if ($checked) return true;
        return false;
    }

    public function checkByUUID($uuid)
    {
        if (empty($uuid)) return false;
        $checked = DB::table('user_permissions')
            ->where('perm_id', $this->id)
            ->where('uuid', $uuid)
            ->first();
        if ($checked) return true;
        return false;
    }

    public function assign($users)
    {
        if ($users instanceof User) {
            $user_permissions[] = [
                'uuid' => $users->uuid,
                'perm_id' => $this->id,
            ];
        } elseif ($users instanceof Collection) {
            foreach ($users as $user) {
                $user_permissions[] = [
                    'uuid' => $user->uuid,
                    'perm_id' => $this->id,    
                ];
            }
        }
        foreach ($user_permissions as $u) {
            DB::table('user_permissions')->updateOrInsert($u, $u);
        }   
        return $this;
    }

    public function assignByUUID($uuids)
    {
        $user_permissions = [];
        if (!is_array($uuids)) {
            $user_permissions[] = [
                'uuid' => $uuids,
                'perm_id' => $this->id,
            ];
        } else {
            foreach ($uuids as $uuid) {
                $user_permissions[] = [
                    'uuid' => $uuid,
                    'perm_id' => $this->id,
                ];
            }
        }
        foreach ($user_permissions as $u) {
            DB::table('user_permissions')->updateOrInsert($u, $u);
        }
        return $this;
    }

    public function removeAll()
    {
        DB::table('user_permissions')->where('perm_id', $this->id)->delete();
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
        DB::table('user_permissions')->where('perm_id', $this->id)->whereIn('uuid', $uuids)->delete();
        return $this;
    }

    public function removeByUUID($uuids)
    {
        if (!is_array($uuids)) {
            $uuids = array($uuids);
        }
        DB::table('user_permissions')->where('perm_id', $this->id)->whereIn('uuid', $uuids)->delete();
        return $this;
    }

}
