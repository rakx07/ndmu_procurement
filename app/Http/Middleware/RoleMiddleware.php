<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            Log::error('User is not authenticated in middleware!');
            return redirect('/login')->with('error', 'You must be logged in to access this page.');
        }

        $userRole = (int) Auth::user()->role;
        $requiredRoles = array_map('intval', $roles);

        Log::info('Middleware Check - User ID: ' . Auth::user()->id . ' | Role: ' . $userRole);
        Log::info('Required Roles (from route middleware): ' . json_encode($requiredRoles));

        // Redirect users to their respective dashboards if they lack the correct role
        if (!in_array($userRole, $requiredRoles, true)) {
            Log::warning('Unauthorized access attempt by user ID: ' . Auth::user()->id . ' | Role: ' . $userRole);
            return redirect($this->redirectToDashboard($userRole))->with('error', 'Access denied. You do not have the required role.');
        }

        return $next($request);
    }

    /**
     * Redirect user to the correct dashboard based on their role.
     */
    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 0:
                return route('staff.dashboard'); // Redirect Staff
            case 2:
                return route('supervisor.dashboard'); // Redirect Supervisor
            case 5:
                return route('it_admin.dashboard'); // Redirect IT Admin
            default:
                return route('home'); // Redirect to home if role is undefined
        }
    }
}
