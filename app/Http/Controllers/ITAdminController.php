<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ITAdminController extends Controller
{
    /**
     * Display the list of users.
     */
    public function index()
    {
        $users = User::with('office')->get();
        $offices = Office::all(); // Fetch offices for selection

        return view('it_admin.index', compact('users', 'offices'));
    }

    /**
     * Show IT Admin Dashboard.
     */
    public function dashboard()
    {
        return view('it_admin.dashboard');
    }

    /**
     * Show the user creation form.
     */
    public function create()
    {
        $offices = Office::all(); // Fetch all offices for dropdown
        return view('it_admin.create', compact('offices'));
    }

    /**
     * Store a new user with a randomly generated temporary password.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'employee_id' => ['required', 'string', 'max:255', 'unique:users'],
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'integer'],
            'office_id' => ['required', 'exists:offices,id'],
        ]);

        // Generate a secure random temporary password
        $tempPassword = Str::random(10);

        // Create the user
        $user = User::create([
            'employee_id' => $request->employee_id,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'email' => strtolower($request->email),
            'password' => Hash::make($tempPassword), // Hash the password
            'role' => $request->role,
            'office_id' => $request->office_id,
            'status' => 1, // Default to active
            'must_change_password' => true, // ✅ Require password change on first login
        ]);

        // Ensure user creation was successful
        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'User creation failed.']);
        }

        return redirect()->back()->with([
            'success' => 'User created successfully!',
            'temp_password' => $tempPassword, // Store temp password in session
        ]);
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|string|max:255|unique:users,employee_id,' . $id,
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|integer',
            'office_id' => 'required|exists:offices,id',
            'status' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'employee_id' => $request->employee_id,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'email' => $request->email,
            'role' => $request->role,
            'office_id' => $request->office_id,
            'status' => $request->status,
        ]);

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Reset a user's password and generate a new temporary password.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $newTempPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($newTempPassword),
            'must_change_password' => true, // ✅ Require password change after reset
        ]);

        return back()->with([
            'success' => 'Password reset successfully! User must change password on next login.',
            'temp_password' => $newTempPassword,
        ]);
    }

    /**
     * Toggle a user's active/inactive status.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status; // Toggle active/inactive
        $user->save();

        return back()->with('success', 'User status updated successfully!');
    }

    /**
     * Suspend a user.
     */
    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->status = 0; // Set user as inactive/suspended
        $user->save();

        return back()->with('success', 'User suspended successfully!');
    }
}
