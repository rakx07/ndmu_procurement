<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementRequest;
use App\Models\Approval;
use App\Models\RequestApprovalHistory; // ✅ Add this import
use Illuminate\Support\Facades\Auth;


class ComptrollerController extends Controller
{
    /**
     * Show the Comptroller dashboard.
     */
    public function dashboard()
    {
        $pendingRequests = ProcurementRequest::where('status', 'pending_comptroller')->get();
        return view('comptroller.dashboard', compact('pendingRequests'));
    }

    /**
     * Show all pending procurement requests for Comptroller approval.
     */
    public function pendingApprovals()
{
    $pendingRequests = ProcurementRequest::with('requestor') // ✅ Ensures requestor data is included
        ->where(function ($query) {
            $query->where('status', 'admin_approved')
                  ->orWhere('status', 'supervisor_approved');
        })
        ->where('comptroller_approved', 0)
        ->get();

    return view('comptroller.pending_approvals', compact('pendingRequests'));
}




    /**
     * Show all approved procurement requests.
     */
    public function approvedRequests()
    {
        $approvedRequests = ProcurementRequest::where('status', 'approved')->get();
        return view('comptroller.approved_requests', compact('approvedRequests'));
    }

    /**
     * Show all rejected procurement requests.
     */
    public function rejectedRequests()
    {
        $rejectedRequests = ProcurementRequest::where('status', 'rejected')->get();
        return view('comptroller.rejected_requests', compact('rejectedRequests'));
    }

    /**
     * Approve a procurement request.
     */
    public function approve($id)
{
    $user = Auth::user();
    $procurementRequest = ProcurementRequest::findOrFail($id);

    $procurementRequest->update(['status' => 'comptroller_approved', 'approved_by' => $user->id]);

    // ✅ Store approval history
    RequestApprovalHistory::create([
        'request_id' => $procurementRequest->id,
        'approver_id' => $user->id,
        'role' => 4, // Comptroller role
        'status' => 'approved',
    ]);

    return redirect()->route('comptroller.dashboard')->with('success', 'Request approved.');
}

    /**
     * Reject a procurement request with remarks.
     */
    public function reject(Request $request, $id)
{
    $procurementRequest = ProcurementRequest::findOrFail($id);
    
    if (!in_array($procurementRequest->status, ['admin_approved', 'supervisor_approved']) || $procurementRequest->comptroller_approved == 1) {
        return redirect()->back()->with('error', 'This request is not eligible for Comptroller rejection.');
    }

    $procurementRequest->status = 'rejected';
    $procurementRequest->remarks = $request->remarks;
    $procurementRequest->approved_by = auth()->user()->id;
    $procurementRequest->save();

    return redirect()->route('comptroller.pending_approvals')->with('success', 'Request rejected successfully.');
}


    /**
     * Show an individual procurement request.
     */
    public function show($id)
    {
        $request = ProcurementRequest::findOrFail($id);
        return view('comptroller.show', compact('request'));
    }
}
