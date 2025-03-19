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
     * Show the Administrator Dashboard (List of requests that need admin approval).
     */
    public function dashboard()
    {
        $pendingRequests = ProcurementRequest::where('status', 'supervisor_approved')
                            ->where('needs_admin_approval', true)
                            ->get();

        return view('admin.dashboard', compact('pendingRequests'));
    }

    /**
     * Show all approved requests.
     */
    public function approvedRequests()
    {
        $approvedRequests = ProcurementRequest::where('status', 'admin_approved')->get();
        return view('admin.approved_requests', compact('approvedRequests'));
    }

    /**
     * Show all rejected requests.
     */
    public function rejectedRequests()
    {
        $rejectedRequests = ProcurementRequest::where('status', 'rejected')->get();
        return view('admin.rejected_requests', compact('rejectedRequests'));
    }

    /**
     * Show details of a specific Procurement Request.
     */
    public function show($id)
    {
        $procurementRequest = ProcurementRequest::findOrFail($id);

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

    if ($procurementRequest->status !== 'supervisor_approved' || !$procurementRequest->needs_admin_approval) {
        return redirect()->route('admin.dashboard')->with('error', 'Only valid supervisor-approved requests can be processed.');
    }

    $procurementRequest->update([
        'status' => 'admin_approved',
        'needs_admin_approval' => false,
        'approved_by' => $user->id
    ]);

    Approval::create([
        'request_id' => $procurementRequest->id,
        'approver_id' => $user->id,
        'role' => $user->role, // ✅ Store actual admin role dynamically
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

        if ($procurementRequest->status !== 'supervisor_approved' || !$procurementRequest->needs_admin_approval) {
            return redirect()->route('admin.dashboard')->with('error', 'Only valid supervisor-approved requests can be rejected.');
        }

        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);

        $procurementRequest->update([
            'status' => 'rejected',
            'needs_admin_approval' => false,
            'remarks' => $request->input('remarks'),
        ]);

        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 3, // ✅ Store role ID instead of 'Administrator'
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Request rejected successfully.');
    }
    public function pendingRequests()
{
    $pendingRequests = ProcurementRequest::where('status', 'supervisor_approved')
                        ->where('needs_admin_approval', true)
                        ->get();

    return view('admin.pending_requests', compact('pendingRequests'));
}

}
