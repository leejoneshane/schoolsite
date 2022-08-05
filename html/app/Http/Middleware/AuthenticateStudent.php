<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\Response;

class AuthenticateStudent
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || Auth::guard($guard)->user()->user_type != 'Student') {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return Response::deny('只有學生才能連結此頁面！');
            }
        }
        return $next($request);
    }
}