<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementRequest;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Show staff dashboard with their procurement requests.
     */
    public function dashboard()
    {
        $requests = ProcurementRequest::where('requestor_id', Auth::id())
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('staff.dashboard', compact('requests'));
    }

    /**
     * Show the procurement request form.
     */
    public function create()
    {
        return view('staff.create'); 
        // Only shows the form, request handling is done in ProcurementRequestController
    }
}
