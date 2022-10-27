<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Messager extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if (Auth::user()) {
            $user = User::find(Auth::user()->id);
            $manager = $user->hasPermission('messager.broadcast');
            if ($user->is_admin || $manager) {
                return view('components.messager', ['broadcast' => true]);
            }
        } else {
            return view('components.messager', ['broadcast' => false]);
        }
    }
}
