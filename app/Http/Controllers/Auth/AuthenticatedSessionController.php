<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ✅ Ensure correct authentication
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate(); // ✅ Ensure session is regenerated

        $user = Auth::user();

        // ✅ Fix: Ensure password is not empty and properly checked
        if (!isset($user->password) || empty($user->password)) {
            Auth::logout(); // Logout the user if password is not set properly
            return redirect()->route('login')->withErrors(['email' => 'Your password is invalid. Please contact support.']);
        }

        // ✅ Ensure first-time users must change password
        if (is_null($user->password_changed_at)) {
            return redirect()->route('change_password_form')->with('warning', 'You must change your password before proceeding.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
