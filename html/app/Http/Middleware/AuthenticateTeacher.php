<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthenticateTeacher
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || Auth::guard($guard)->user()->user_type != 'Teacher') {
            if ($request->ajax() || $request->wantsJson()) {
                return response('只有教職員才能連結此頁面！', 401);
            } else {
                throw new AccessDeniedHttpException('只有教職員才能連結此頁面！');
            }
        }
        return $next($request);
    }
}