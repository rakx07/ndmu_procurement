<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\Approval;
use App\Models\User;
use App\Models\RequestApprovalHistory; // ✅ Add this import

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
            $query->where('supervisor_id', $user->id)
                  ->where('office_id', $user->office_id);
        })->where('status', 'pending')->get();

        return view('supervisor.dashboard', compact('requests'));
    }

    /**
     * Show details of a specific Procurement Request.
     */
    public function show($id)
    {
        $procurementRequest = ProcurementRequest::findOrFail($id);

        // Ensure Supervisor can only view requests from their assigned Staff
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

        // Ensure Supervisor can only approve requests from their assigned Staff in the same office
        if ($procurementRequest->requestor->supervisor_id !== $user->id || $procurementRequest->requestor->office_id !== $user->office_id) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to approve this request.');
        }

        // Move status to next stage
        $procurementRequest->update([
            'status' => 'supervisor_approved',
            'approved_by' => $user->id
        ]);

        // Store approval record
        Approval::create([
            'office_req_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => $user->role, // ✅ Use role ID
            'status' => 'supervisor_approved',
        ]);

        // Store approval history
        RequestApprovalHistory::create([
            'office_req_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 2, // Supervisor role
            'status' => 'approved',
            'remarks' => 'Approved by Supervisor',
            'created_at' => now(),
            'updated_at' => now()
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

        // Ensure the Supervisor can only reject requests from their assigned Staff in the same office
        if ($procurementRequest->requestor->supervisor_id !== $user->id || $procurementRequest->requestor->office_id !== $user->office_id) {
            return redirect()->route('supervisor.dashboard')->with('error', 'Unauthorized to reject this request.');
        }

        $remarks = strip_tags($request->input('remarks')); // Security: Remove HTML tags

        $procurementRequest->update([
            'status' => 'rejected',
            'remarks' => $remarks,
        ]);

        // Store rejection record
        Approval::create([
            'office_req_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => $user->role, // ✅ Use role ID
            'status' => 'rejected',
            'remarks' => $remarks,
        ]);

        // Store rejection history
        RequestApprovalHistory::create([
            'office_req_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 2, // Supervisor role
            'status' => 'rejected',
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('supervisor.dashboard')->with('success', 'Request rejected successfully.');
    }

    /**
     * Fetch items of a Procurement Request.
     */
    public function getRequestItems($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::with('items')->findOrFail($id);

        // Ensure the Supervisor can only view their assigned staff's requests
        if ($procurementRequest->requestor->supervisor_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        return response()->json($procurementRequest->items);
    }

    /**
     * List all approved requests by the Supervisor.
     */
    public function approvedRequests()
    {
        $user = Auth::user();

        // Fetch requests approved by this supervisor
        $approvedRequests = Approval::where('approver_id', $user->id)
            ->where('status', 'supervisor_approved')
            ->with(['requestor']) // Ensure requestor relationship is loaded
            ->get();

        return view('supervisor.approved_list_requests', compact('approvedRequests'));
    }

    /**
     * Fetch items of an approved Procurement Request.
     */
    public function getApprovedRequestItems($id)
{
    $user = Auth::user();
    
    $procurementRequest = ProcurementRequest::with('items', 'requestor')->findOrFail($id);

    // ✅ Define access conditions:
    $isRequestor = $procurementRequest->requestor_id === $user->id; // The staff who created it
    $isSupervisor = $procurementRequest->requestor->supervisor_id === $user->id; // Supervisor assigned to the staff
    $isComptroller = $user->role === 4; // Comptroller (Role ID: 4) can view all
    $isAdministrator = $user->role === 3; // Administrator (Role ID: 3) can view all
    $isSameOffice = $procurementRequest->office_id === $user->office_id; // Users in the same office can view

    // ✅ Allow access if the user meets at least one of these conditions
    if (!$isRequestor && !$isSupervisor && !$isComptroller && !$isAdministrator && !$isSameOffice) {
        return response()->json(['error' => 'Unauthorized access'], 403);
    }

    return response()->json($procurementRequest->items);
}


    /**
     * List all rejected requests by the Supervisor.
     */
    public function rejectedRequests()
    {
        $user = Auth::user();
        $requests = ProcurementRequest::where('status', 'rejected')
            ->where('office_id', $user->office_id)
            ->latest()
            ->paginate(10);

        return view('supervisor.rejected_requests', compact('requests'));
    }

    /**
     * Fetch items of a rejected Procurement Request.
     */
    public function getRejectedRequestItems($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::with('items')->findOrFail($id);

        // Ensure the Supervisor can only view items of rejected requests in their office
        if ($procurementRequest->status !== 'rejected' || $procurementRequest->office_id !== $user->office_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        return response()->json($procurementRequest->items);
    }
}
