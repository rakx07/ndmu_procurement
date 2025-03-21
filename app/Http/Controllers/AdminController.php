<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\RequestApprovalHistory;
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
                            ->with('requestor') // ✅ Load requestor relationship
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
     * Approve a Procurement Request as an Administrator.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        $procurementRequest->update(['status' => 'admin_approved', 'approved_by' => $user->id]);

        // ✅ Store approval history (No changes made)
        RequestApprovalHistory::create([
            'office_req_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 3, // Administrator role
            'remarks' => 'Approved by Administrator',
            'status' => 'approved',
        ]);

        // ✅ Fix: Ensure the status stored in `approvals` matches ENUM values
        Approval::create([
            'office_req_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => $user->role, // ✅ Use role ID
            'status' => 'admin_approved', // ✅ Ensure this is in ENUM list
            'created_at' => now(), // ✅ Explicit timestamps to prevent issues
            'updated_at' => now(),
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
            'office_req_id' => $procurementRequest->id,
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
                        ->with('requestor') // ✅ Ensure requestor is loaded
                        ->get();


    return view('admin.pending_requests', compact('pendingRequests'));
}

}
