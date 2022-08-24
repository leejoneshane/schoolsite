<?php
 
namespace App\Policies;
 
use App\Models\IcsEvent;
use App\Models\User;
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
            if (strpos($event->calendar()->summary, '學生')) {
                return true;      
            }
        }
        return false;
        //return Response::deny('您只能閱覽學生行事曆.');
    }

    public function create(User $user)
    {
        if ($user->user_type == 'Teacher') {
            if ($user->profile['role_id'] == 'C02' ||
                $user->profile['role_id'] == 'C03') {
                return true;
            }
        }
        return false;
        //return Response::deny('您沒有權限編輯校內行事曆.');
    }

    public function update(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            if (substr($event->unit_id, 0, 3) == substr($user->profile['unit_id'], 0, 3) && (
                $user->profile['role_id'] == 'C02' ||
                $user->profile['role_id'] == 'C03')) {
                return true;
            }
        }
        return false;
        //return Response::deny('您無法編輯其他處室建立的行事曆事件！');
    }

    public function delete(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            if ($event->unit_id == substr($user->profile['unit_id'], 0, 3) &&
                $user->profile['role_id'] == 'C02') {
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