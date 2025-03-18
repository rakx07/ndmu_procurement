<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\Approval;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show the Administrator Dashboard (List of supervisor-approved requests that need admin approval).
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch procurement requests that are approved by a supervisor and require Administrator approval
        $pendingRequests = ProcurementRequest::where('status', 'supervisor_approved')
                            ->where('needs_admin_approval', true)
                            ->get();

        return view('admin.dashboard', compact('pendingRequests'));
    }

    /**
     * Show details of a specific Procurement Request.
     */
    public function show($id)
    {
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure only requests that need admin approval are accessible
        if ($procurementRequest->status !== 'supervisor_approved' || !$procurementRequest->needs_admin_approval) {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized to view this request.');
        }

        return view('admin.show_request', compact('procurementRequest'));
    }

    /**
     * Approve a Procurement Request as an Administrator.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure only supervisor-approved requests that need admin approval can be processed
        if ($procurementRequest->status !== 'supervisor_approved' || !$procurementRequest->needs_admin_approval) {
            return redirect()->route('admin.dashboard')->with('error', 'Only valid supervisor-approved requests can be processed.');
        }

        // Update procurement request status
        $procurementRequest->update([
            'status' => 'admin_approved',
            'needs_admin_approval' => false, // No longer needs admin approval
            'approved_by' => $user->id
        ]);

        // Store approval record
        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 'Administrator',
            'status' => 'admin_approved',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Request approved successfully.');
    }

    /**
     * Reject a Procurement Request as an Administrator.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure only supervisor-approved requests that need admin approval can be rejected
        if ($procurementRequest->status !== 'supervisor_approved' || !$procurementRequest->needs_admin_approval) {
            return redirect()->route('admin.dashboard')->with('error', 'Only valid supervisor-approved requests can be rejected.');
        }

        // Validate the remarks field (ensure it's provided)
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);

        // Update procurement request status
        $procurementRequest->update([
            'status' => 'rejected',
            'needs_admin_approval' => false,
            'remarks' => $request->input('remarks'),
        ]);

        // Store rejection record
        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 'Administrator',
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Request rejected successfully.');
    }
}
