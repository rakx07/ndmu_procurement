<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ITAdminController extends Controller
{
    /**
     * Display the list of users.
     */
    public function index()
    {
        // Fetch all users with their office details
        $users = User::with('office')->get();
        $offices = Office::all(); // Fetch offices for the edit modal

        return view('it_admin.index', compact('users', 'offices'));
    }
    public function dashboard()
{
    return view('it_admin.dashboard');
}
    /**
     * Show the user creation form.
     */
    public function create()
    {
        $offices = Office::all(); // Fetch all offices for the dropdown
        return view('it_admin.create', compact('offices'));
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'employee_id' => ['required', 'string', 'max:255', 'unique:users'],
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'integer'],
            'office_id' => ['required', 'exists:offices,id'], // Validate office selection
        ]);

        // Generate a temporary password
        $tempPassword = 'Temp' . rand(1000, 9999);

        // Create the user
        $user = User::create([
            'employee_id' => $request->employee_id,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'email' => strtolower($request->email),
            'password' => Hash::make($tempPassword),
            'role' => $request->role,
            'office_id' => $request->office_id,
            'status' => 1, // Default to active
        ]);

        return redirect()->back()->with('success', 'User created successfully! Temporary password: ' . $tempPassword);
    }

    /**
     * Update a user.
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
        'status' => 'required|boolean', // ✅ Ensure status is validated
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
        'status' => $request->status, // ✅ Ensure status is updated
    ]);

    return back()->with('success', 'User updated successfully!');
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
