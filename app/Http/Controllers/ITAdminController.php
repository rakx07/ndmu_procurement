<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ITAdminController extends Controller
{
    /**
     * Display the list of users.
     */
    public function index()
    {
        $users = User::with('office')->get();
        $offices = Office::all();
        $supervisors = User::where('role', 2)->get(); // Get all users with role 2 (Supervisors)
        $administrators = User::where('role', 3)->get(); // Get all users with role 3 (Administrators)

        return view('it_admin.index', compact('users', 'offices', 'supervisors', 'administrators'));
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
        $offices = Office::all();
        $supervisors = User::where('role', 2)->get(); // Fetch Supervisors
        $administrators = User::where('role', 3)->get(); // Fetch Administrators
    
        return view('it_admin.create', compact('offices', 'supervisors', 'administrators'));
    }

    /**
     * Store a new user with a randomly generated temporary password.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'employee_id' => 'required|string|max:255|unique:users',
        'lastname' => 'required|string|max:255',
        'firstname' => 'required|string|max:255',
        'middlename' => 'nullable|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:users',
        'role' => 'required|integer',
        'office_id' => 'required|exists:offices,id',
        'supervisor_id' => 'nullable|exists:users,id', // Assigned if Role = Staff
        'administrator_id' => 'nullable|exists:users,id', // Assigned if Role = Supervisor
    ]);

    $tempPassword = Str::random(10);

    $user = User::create([
        'employee_id' => $request->employee_id,
        'lastname' => $request->lastname,
        'firstname' => $request->firstname,
        'middlename' => $request->middlename,
        'email' => strtolower($request->email),
        'password' => Hash::make($tempPassword),
        'role' => $request->role,
        'office_id' => $request->office_id,
        'supervisor_id' => ($request->role == 0) ? $request->supervisor_id : null, // Only for Staff
        'administrator_id' => ($request->role == 0 || $request->role == 2) ? $request->administrator_id : null, // Staff & Supervisor
        'status' => 1,
        'must_change_password' => true,
    ]);

    if (!$user) {
        return redirect()->back()->withErrors(['error' => 'User creation failed.']);
    }

    return redirect()->back()->with([
        'success' => 'User created successfully!',
        'email' => $user->email, // Return email as well
        'temp_password' => $tempPassword,
    ]);
}


    /**
     * Update an existing user.
     */
    public function update(Request $request, $id)
{
    Log::info("✅ Update request received: ", $request->all());

    $validatedData = $request->validate([
        'employee_id' => 'required|string|max:255|unique:users,employee_id,' . $id,
        'lastname' => 'required|string|max:255',
        'firstname' => 'required|string|max:255',
        'middlename' => 'nullable|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $id,
        'role' => 'required|integer',
        'office_id' => 'nullable|exists:offices,id',
        'status' => 'nullable|in:0,1',
        'supervisor_id' => 'nullable|exists:users,id',
        'administrator_id' => 'nullable|exists:users,id',
    ]);

    Log::debug("✅ Validated data: ", $validatedData);

    $user = User::findOrFail($id);
    Log::info("✅ Current user data before update: ", $user->toArray());

    // Ensure `status` and `role` are properly converted
    $user->fill([
        'employee_id' => $request->employee_id,
        'lastname' => $request->lastname,
        'firstname' => $request->firstname,
        'middlename' => $request->middlename,
        'email' => $request->email,
        'role' => (int) $request->role,
        'office_id' => $request->office_id,
        'status' => (int) ($request->status ?? $user->status),  // Use existing if not provided
        'supervisor_id' => ($request->role == 0) ? $request->supervisor_id : null,
        'administrator_id' => ($request->role == 0 || $request->role == 2) ? $request->administrator_id : null,
    ]);

    if ($user->isDirty()) {  // ✅ Check if changes are detected
        Log::info("🔄 Changes detected, updating user...");
        $user->save();
        Log::info("✅ User updated successfully: ", $user->toArray());
        return back()->with('success', 'User updated successfully!');
    } else {
        Log::warning("⚠️ No changes detected, update skipped for user ID: $id");
        return back()->with('warning', 'No changes were made.');
    }
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
