<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            Log::error('User is not authenticated in middleware!');
            return redirect('/login');
        }

        $userRole = (int) Auth::user()->role;
        $requiredRoles = array_map('intval', $roles);

        Log::info('Middleware Check - User ID: ' . Auth::user()->id . ' | Role: ' . $userRole);
        Log::info('Required Roles (from route middleware): ' . json_encode($requiredRoles));

        if (!in_array($userRole, $requiredRoles, true)) {
            Log::warning('Unauthorized access attempt by user ID: ' . Auth::user()->id);
            abort(403, 'Unauthorized action. Your role: ' . $userRole . ' | Required roles: ' . json_encode($requiredRoles));
        }

        return $next($request);
    }
}
