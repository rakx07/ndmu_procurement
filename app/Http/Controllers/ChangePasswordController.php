<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Ensure User model is imported

class ChangePasswordController extends Controller
{
    /**
     * Show the password change form.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Handle password update request.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('change_password_form')->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password and reset "must_change_password" field
        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }
}
