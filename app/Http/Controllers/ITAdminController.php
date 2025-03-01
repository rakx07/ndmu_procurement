<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ITAdminController extends Controller
{
    /**
     * Show the user creation form.
     */
    public function create()
    {
        return view('it_admin.create');
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
          
        ]);

        return redirect()->back()->with('success', 'User created successfully! Temporary password: ' . $tempPassword);
    }
    public function toggleStatus($id)
{
    $user = User::findOrFail($id);
    $user->status = !$user->status; // Toggle active/inactive
    $user->save();

    return back()->with('success', 'User status updated successfully!');
}

public function suspend($id)
{
    $user = User::findOrFail($id);
    $user->status = 0; // Set user as inactive/suspended
    $user->save();

    return back()->with('success', 'User suspended successfully!');
}

public function index()
{
    // Fetch all users
    $users = User::all();

    // Load 'it_admin/index.blade.php' as the IT Admin dashboard
    return view('it_admin.index', compact('users'));
}
}
