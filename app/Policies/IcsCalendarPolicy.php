<?php
 
namespace App\Policies;
 
use App\Models\IcsCalendar;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class IcsCalendarPolicy
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

    public function view(User $user, IcsCalendar $cal)
    {
        if ($user->user_type == 'Teacher') {
            return true;
        } else {
            if (strpos($cal->summary, '學生')) {
                return true;      
            }
        }
        return Response::deny('您只能閱覽學生行事曆.');
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, IcsCalendar $cal)
    {
        //
    }

    public function delete(User $user, IcsCalendar $cal)
    {
        //
    }

    public function restore(User $user, IcsCalendar $cal)
    {
        //
    }

    public function forceDelete(User $user, IcsCalendar $cal)
    {
        //
    }

}