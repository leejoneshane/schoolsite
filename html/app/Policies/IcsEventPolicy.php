<?php

namespace App\Policies;

use App\Models\IcsEvent;
use App\Models\User;
use App\Models\Unit;
use App\Models\Role;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class IcsEventPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            return true;
        } else {
            if (strpos($event->calendar->summary, '學生')) {
                return true;      
            }
        }
        return false;
        //return Response::deny('您只能閱覽學生行事曆.');
    }

    public function create(User $user)
    {
        if ($user->user_type == 'Teacher') {
            $role = Role::find($user->profile->role_id);
            if ( $role->role_no == 'C02' ||
                $role->role_no == 'C03') {
                return true;
            }
        }
        return false;
        //return Response::deny('您沒有權限編輯校內行事曆.');
    }

    public function update(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            if ($event->uuid == $user->uuid) return true;
            $role = Role::find($user->profile->role_id);
            $unit_no = Unit::find($event->unit_id)->unit_no;
            if ($unit_no == substr($role->unit->unit_no, 0, 3) && (
                $role->role_no == 'C02' ||
                $role->role_no == 'C03')) {
                return true;
            }
        }
        return false;
        //return Response::deny('您無法編輯其他處室建立的行事曆事件！');
    }

    public function delete(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            $role = Role::find($user->profile->role_id);
            $unit_no = Unit::find($event->unit_id)->unit_no;
            if ($unit_no == substr($role->unit->unit_no, 0, 3) &&
                $role->role_no == 'C02') {
                return true;
            }
            if ($unit_no == substr($role->unit->unit_no, 0, 3) &&
                $user->uuid == $event->uuid) {
                return true;
            }
        }
        return false;
        //return falseResponse::deny('只有處室主任可以刪除該處室的行事曆事件！');;
    }

    public function restore(User $user, IcsEvent $event)
    {
        //
    }

    public function forceDelete(User $user, IcsEvent $event)
    {
        //
    }

}