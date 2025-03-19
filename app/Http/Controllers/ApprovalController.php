<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use App\Models\ProcurementRequest;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Approve a procurement request at the current approval level.
     */
    public function approve(Request $request, $requestId)
    {
        $user = Auth::user();
        $role = $user->role; // Get the approver's role

        // Define status mapping for each role
        $statusMap = [
            2 => 'supervisor_approved',
            3 => 'admin_approved',
            4 => 'comptroller_approved',
        ];

        if (!isset($statusMap[$role])) {
            return redirect()->back()->with('error', 'Unauthorized approval.');
        }

        // Check if this user has already approved the request
        $existingApproval = Approval::where('request_id', $requestId)
            ->where('approver_id', $user->id)
            ->where('role', $role)
            ->exists();

        if ($existingApproval) {
            return redirect()->back()->with('error', 'You have already approved this request.');
        }

        // Create a new approval record
        Approval::create([
            'request_id' => $requestId,
            'approver_id' => $user->id,
            'role' => $role,
            'status' => $statusMap[$role],
            'remarks' => $request->input('remarks') ?? null,
        ]);

        return redirect()->back()->with('success', 'Request approved successfully.');
    }

    /**
     * Reject a procurement request.
     */
    public function reject(Request $request, $requestId)
    {
        $user = Auth::user();

        Approval::create([
            'request_id' => $requestId,
            'approver_id' => $user->id,
            'role' => $user->role,
            'status' => 'rejected',
            'remarks' => $request->input('remarks') ?? 'No remarks provided',
        ]);

        return redirect()->back()->with('error', 'Request has been rejected.');
    }

    /**
     * Display approval history for a request.
     */
    public function showRequestApprovals($requestId)
    {
        $approvals = Approval::where('request_id', $requestId)
                ->orderBy('created_at', 'asc')
                ->get();

        return view('procurement.request_approvals', compact('approvals'));
    }
}
