<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\Approval;
use App\Models\RequestApprovalHistory; // ✅ Add this import
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

    $procurementRequest->update(['status' => 'admin_approved', 'approved_by' => $user->id]);

    // ✅ Store approval history
    RequestApprovalHistory::create([
        'request_id' => $procurementRequest->id,
        'approver_id' => $user->id,
        'role' => 3, // Administrator role
        'status' => 'approved',
    ]);

    return redirect()->route('admin.dashboard')->with('success', 'Request approved.');
}

    /**
     * Reject a Procurement Request as an Administrator.
     */
    public function reject(Request $request, $id)
{
    $user = Auth::user();
    $procurementRequest = ProcurementRequest::findOrFail($id);
    $remarks = strip_tags($request->input('remarks'));

    $procurementRequest->update(['status' => 'rejected', 'remarks' => $remarks]);

    // ✅ Store rejection history
    RequestApprovalHistory::create([
        'request_id' => $procurementRequest->id,
        'approver_id' => $user->id,
        'role' => 3, // Administrator role
        'status' => 'rejected',
        'remarks' => $remarks,
    ]);

    return redirect()->route('admin.dashboard')->with('success', 'Request rejected.');
}


    public function pendingRequests()
{
    $pendingRequests = ProcurementRequest::where('status', 'supervisor_approved')
                        ->where('needs_admin_approval', true)
                        ->with('user') // ✅ Eager load the user
                        ->get();

    return view('admin.pending_requests', compact('pendingRequests'));
}

}
