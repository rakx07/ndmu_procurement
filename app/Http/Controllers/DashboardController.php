<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
        {
            $user = auth()->user();

            if (!$user) {
                return redirect()->route('login');
            }

            if ($user->role == 0) {
                return redirect()->route('staff.dashboard');
            } elseif ($user->role == 5) {
                return redirect()->route('it_admin.dashboard');
            }

            return view('dashboard'); // Default dashboard for other roles
        }

}
