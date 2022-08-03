<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class AuthenticateAdmin
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || (!Auth::guard($guard)->user()->is_admin && Auth::guard($guard)->user()->id != 1)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                session()->flash('error', '只有管理員才能使用管理介面！');
                return redirect()->route('home');
            }
        }
        return $next($request);
    }
}