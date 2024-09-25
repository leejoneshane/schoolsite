<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\GameCharacter;

class NoImage
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->user()->user_type == 'Student') {
            $user = Auth::guard($guard)->user();
            $character = GameCharacter::find($user->uuid); 
            if ($character->image) {
                return $next($request);
            } else {
                return redirect()->route('game.player_image');
            }
        }
        return redirect()->route('login');
    }
}
