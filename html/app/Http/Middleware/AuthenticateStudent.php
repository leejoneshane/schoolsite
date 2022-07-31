<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class AuthenticateStudent
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || Auth::guard($guard)->user()->user_type != 'Student') {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect('login');
            }
        }
        return $next($request);
    }
}