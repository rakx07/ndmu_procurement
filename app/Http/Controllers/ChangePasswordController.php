<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Ensure the User model is imported

class ChangePasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('auth.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Ensure $user is an instance of User before calling save()
        if ($user instanceof User) {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = now();
            $user->save();
        }

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }
}
