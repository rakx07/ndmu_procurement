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
     * Show the Supervisor Dashboard (List of requests from their Staff).
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch only requests from the Supervisor's assigned Staff
        $requests = ProcurementRequest::whereHas('requestor', function ($query) use ($user) {
            $query->where('supervisor_id', $user->id);
        })->get();

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
     * Show the confirmation page before approving a request.
     */
    public function approveRequestView($id)
    {
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure Supervisor can only approve requests from their Staff
        if ($procurementRequest->requestor->supervisor_id !== Auth::id()) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to approve this request.');
        }

        return view('supervisor.approve_request', compact('procurementRequest'));
    }

    /**
     * Approve a Procurement Request.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure the Supervisor can only approve their Staff's requests
        if ($procurementRequest->requestor->supervisor_id !== $user->id) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to approve this request.');
        }

        $procurementRequest->update([
            'status' => 'approved',
            'approved_by' => $user->id
        ]);

        // Store approval record
        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 'Supervisor',
            'status' => 'approved',
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
        if ($procurementRequest->requestor->supervisor_id !== $user->id) {
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
