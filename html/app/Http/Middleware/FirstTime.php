<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class FirstTime
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (! User::first()) {
            return redirect('register');
        }
        return $next($request);
    }
}
