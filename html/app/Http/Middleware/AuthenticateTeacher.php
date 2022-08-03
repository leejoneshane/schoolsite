<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class AuthenticateTeacher
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || Auth::guard($guard)->user()->user_type != 'Teacher') {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                session()->flash('error', '只有教師才能連結此頁面！');
                return redirect()->route('home');
            }
        }
        return $next($request);
    }
}