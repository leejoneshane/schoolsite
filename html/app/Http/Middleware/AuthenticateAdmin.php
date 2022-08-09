<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthenticateAdmin
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || (!Auth::guard($guard)->user()->is_admin && Auth::guard($guard)->user()->id != 1)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('只有管理員才能使用管理介面！', 401);
            } else {
                throw new AccessDeniedHttpException('只有管理員才能使用管理介面！');
            }
        }
        return $next($request);
    }
}