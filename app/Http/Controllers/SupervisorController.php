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
        $requests = ProcurementRequest::whereHas('user', function ($query) use ($user) {
            $query->where('supervisor_id', $user->id);
        })->get();

        return view('supervisor.dashboard', compact('requests'));
    }

    /**
     * Show details of a specific Procurement Request.
     */
    public function show($id)
    {
        $request = ProcurementRequest::findOrFail($id);

        // Ensure Supervisor can only view requests from their Staff
        if ($request->user->supervisor_id !== Auth::id()) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to view this request.');
        }

        return view('supervisor.show_request', compact('request'));
    }

    /**
     * Show the confirmation page before approving a request.
     */
    public function approveRequestView($id)
    {
        $request = ProcurementRequest::findOrFail($id);

        // Ensure Supervisor can only approve requests from their Staff
        if ($request->user->supervisor_id !== Auth::id()) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to approve this request.');
        }

        return view('supervisor.approve_request', compact('request'));
    }

    /**
     * Approve a Procurement Request.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure the Supervisor can only approve their Staff's requests
        if ($procurementRequest->user->supervisor_id !== $user->id) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to approve this request.');
        }

        $procurementRequest->status = 'approved';
        $procurementRequest->approved_by = $user->id;
        $procurementRequest->save();

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
        if ($procurementRequest->user->supervisor_id !== $user->id) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to reject this request.');
        }

        $procurementRequest->status = 'rejected';
        $procurementRequest->remarks = $request->input('remarks');
        $procurementRequest->save();

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
