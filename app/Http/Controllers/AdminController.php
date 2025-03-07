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
     * Show the Administrator Dashboard (List of supervisor-approved requests).
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch procurement requests that have been approved by a supervisor but not yet by an administrator
        $requests = ProcurementRequest::where('status', 'supervisor_approved')->get();

        return view('admin.dashboard', compact('requests'));
    }

    /**
     * Show details of a specific Procurement Request.
     */
    public function show($id)
    {
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure only supervisor-approved requests are accessible
        if ($procurementRequest->status !== 'supervisor_approved') {
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

        // Ensure only supervisor-approved requests can be processed
        if ($procurementRequest->status !== 'supervisor_approved') {
            return redirect()->route('admin.dashboard')->with('error', 'Only supervisor-approved requests can be processed.');
        }

        $procurementRequest->update([
            'status' => 'admin_approved',
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

        // Ensure only supervisor-approved requests can be rejected by the administrator
        if ($procurementRequest->status !== 'supervisor_approved') {
            return redirect()->route('admin.dashboard')->with('error', 'Only supervisor-approved requests can be rejected.');
        }

        $procurementRequest->update([
            'status' => 'rejected',
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
