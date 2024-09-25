<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Student
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->user() && Auth::guard($guard)->user()->user_type == 'Student') {
            return $next($request);
        }
        return redirect()->route('login')->with('error', '只有學生可以使用此介面！');
    }
}
