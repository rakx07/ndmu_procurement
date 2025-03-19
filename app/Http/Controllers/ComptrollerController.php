<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementRequest;
use App\Models\Approval;

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
    $request = ProcurementRequest::findOrFail($id);
    
    // Ensure the request is actually pending for Comptroller approval
    if (!in_array($request->status, ['admin_approved', 'supervisor_approved']) || $request->comptroller_approved == 1) {
        return redirect()->back()->with('error', 'This request is not eligible for Comptroller approval.');
    }

    // Update the procurement request as approved by the Comptroller
    $request->comptroller_approved = 1;
    $request->status = 'comptroller_approved'; // Updating status for tracking
    $request->approved_by = auth()->user()->id;
    $request->save();

    // Insert or update the approval entry in the approvals table
    Approval::updateOrCreate(
        [
            'request_id' => $id, 
            'approver_id' => auth()->id(), 
            'role' => 4 // Role 4 = Comptroller
        ],
        [
            'status' => 'comptroller_approved', 
            'updated_at' => now()
        ]
    );

    return redirect()->route('comptroller.pending_approvals')->with('success', 'Request approved successfully.');
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
