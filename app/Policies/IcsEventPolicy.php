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
        return Response::deny('您只能閱覽學生行事曆.');
    }

    public function create(User $user)
    {
        if ($user->user_type == 'Teacher') {
            $teacher = $user->profile();
            $jobs = $teacher->roles();
            foreach ($jobs as $job) {
                if (strpos($job->name, '主任') || strpos($job->name, '組長')) {
                    return true;
                }        
            }
        }
        return Response::deny('您沒有權限編輯校內行事曆.');
    }

    public function update(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            $teacher = $user->profile();
            $jobs = $teacher->roles();
            foreach ($jobs as $job) {
                if ((strpos($job->name, '主任') || strpos($job->name, '組長')) && $job->unit_id == $event->unit_id) {
                    return true;
                }        
            }
        }
        return Response::deny('您無法編輯其他處室建立的行事曆事件！');
    }

    public function delete(User $user, IcsEvent $event)
    {
        if ($user->user_type == 'Teacher') {
            $teacher = $user->profile();
            $jobs = $teacher->roles();
            foreach ($jobs as $job) {
                if (strpos($job->name, '主任') && $job->unit_id == $event->unit_id) {
                    return true;
                }        
            }
        }
        return falseResponse::deny('只有處室主任可以刪除該處室的行事曆事件！');;
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