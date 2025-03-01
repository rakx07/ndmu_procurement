<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'employee_id' => ['required', 'string', 'max:255', 'unique:users'],
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'integer'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the user
        $user = User::create([
            'employee_id' => $request->employee_id,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
         
        ]);

        // Fire the Registered event (useful for email verification, etc.)
        event(new Registered($user));

        // Auto-login the user
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }
}
