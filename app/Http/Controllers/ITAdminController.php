<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ITAdminController extends Controller
{
    public function create()
    {
        return view('it_admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:users',
            'lastname' => 'required',
            'firstname' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'required|integer',
            'designation' => 'required',
        ]);

        // Generate a temporary password
        $tempPassword = 'temp' . rand(1000, 9999);

        User::create([
            'employee_id' => $request->employee_id,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => $request->role,
            'designation' => $request->designation,
        ]);

        return redirect()->back()->with('success', 'User created successfully. Temporary password: ' . $tempPassword);
    }
}
