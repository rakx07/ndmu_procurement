<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestApprovalHistory; // ✅ Fixed import
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

        $procurementRequest = ProcurementRequest::findOrFail($requestId);

        // ✅ Prevent duplicate approvals
        $existingApproval = RequestApprovalHistory::where('office_req_id', $requestId)
            ->where('approver_id', $user->id)
            ->where('role', $role)
            ->exists();

        if ($existingApproval) {
            return redirect()->back()->with('error', 'You have already approved this request.');
        }

        // ✅ Update Procurement Request
        $procurementRequest->update([
            'status' => $statusMap[$role],
            'approved_by' => $user->id
        ]);

        // ✅ Create Approval History
        RequestApprovalHistory::create([
            'office_req_id' => $requestId,
            'approver_id' => $user->id,
            'role' => $role,
            'status' => 'approved',
            'remarks' => $request->input('remarks') ?? 'No remarks provided',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Request approved successfully.');
    }

    /**
     * Reject a procurement request.
     */
    public function reject(Request $request, $requestId)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($requestId);
        $remarks = $request->input('remarks') ?? 'No remarks provided';

        // ✅ Prevent duplicate rejections
        $existingRejection = RequestApprovalHistory::where('office_req_id', $requestId)
            ->where('approver_id', $user->id)
            ->where('role', $user->role)
            ->where('status', 'rejected')
            ->exists();

        if ($existingRejection) {
            return redirect()->back()->with('error', 'You have already rejected this request.');
        }

        // ✅ Update Procurement Request
        $procurementRequest->update([
            'status' => 'rejected',
            'remarks' => $remarks,
        ]);

        // ✅ Create Rejection History
        RequestApprovalHistory::create([
            'office_req_id' => $requestId,
            'approver_id' => $user->id,
            'role' => $user->role,
            'status' => 'rejected',
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('error', 'Request has been rejected.');
    }
}
