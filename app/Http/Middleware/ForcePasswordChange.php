<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->must_change_password && !$request->is('change-password')) {
            return redirect()->route('change_password_form');
        }

        return $next($request);
    }
}
