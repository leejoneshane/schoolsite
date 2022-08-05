<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\Response;

class AuthenticateTeacher
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || Auth::guard($guard)->user()->user_type != 'Teacher') {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                Request::deny('只有教職員才能連結此頁面！');
            }
        }
        return $next($request);
    }
}