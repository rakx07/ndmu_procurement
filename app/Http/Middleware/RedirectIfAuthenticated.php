<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Check if the user must change their password
                if ($user->must_change_password) {
                    return redirect()->route('change_password');
                }

                return redirect('/dashboard'); // Redirect authenticated users to dashboard
            }
        }

        return $next($request);
    }
}
