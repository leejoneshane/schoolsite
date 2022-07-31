<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class NoUser
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (User::first()) {
            return redirect('login');
        }
        return $next($request);
    }
}
