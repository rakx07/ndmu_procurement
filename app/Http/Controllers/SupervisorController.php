<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\Approval;
use App\Models\User;

class SupervisorController extends Controller
{
    /**
     * Show the Supervisor Dashboard (List of requests from their assigned Staff in their office).
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch only procurement requests from staff under the supervisor and in the same office
        $requests = ProcurementRequest::whereHas('requestor', function ($query) use ($user) {
            $query->where('supervisor_id', $user->id)->where('office_id', $user->office_id);
        })->where('status', 'pending')->get();

        return view('supervisor.dashboard', compact('requests'));
    }

    /**
     * Show details of a specific Procurement Request.
     */
    public function show($id)
    {
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure Supervisor can only view requests from their Staff
        if ($procurementRequest->requestor->supervisor_id !== Auth::id()) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to view this request.');
        }

        return view('supervisor.show_request', compact('procurementRequest'));
    }

    /**
     * Approve a Procurement Request.
     */
    public function approve($id)
{
    $user = Auth::user();
    $procurementRequest = ProcurementRequest::findOrFail($id);

    // Ensure Supervisor can only approve requests from their Staff
    if ($procurementRequest->requestor->supervisor_id !== $user->id || $procurementRequest->requestor->office_id !== $user->office_id) {
        return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to approve this request.');
    }

    // Move status to next stage
    $procurementRequest->update([
        'status' => 'supervisor_approved', // Correct ENUM value
        'approved_by' => $user->id
    ]);

    // Store approval record
    Approval::create([
        'request_id' => $procurementRequest->id,
        'approver_id' => $user->id,
        'role' => $user->role, // ✅ Correct: Use role ID (integer)
        'status' => 'supervisor_approved',
    ]);

    return redirect()->route('supervisor.dashboard')->with('success', 'Request approved successfully.');
}


    /**
     * Reject a Procurement Request.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure the Supervisor can only reject their Staff's requests
        if ($procurementRequest->requestor->supervisor_id !== $user->id || $procurementRequest->requestor->office_id !== $user->office_id) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to reject this request.');
        }

        $procurementRequest->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        // Store rejection record
        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 'Supervisor',
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('supervisor.dashboard')->with('success', 'Request rejected successfully.');
    }
}
