<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\User;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class ProcurementRequestController extends Controller
{
    /**
     * Display the list of procurement requests for approval.
     */
    public function index()
    {
        $user = Auth::user();

        // If Supervisor, fetch only requests from their direct Staff
        if ($user->isRole('supervisor')) {
            $requests = ProcurementRequest::whereHas('user', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);
            })->get();
        }
        // If Administrator, fetch requests from their Supervisors & Staff
        elseif ($user->isRole('admin')) {
            $requests = ProcurementRequest::whereHas('user', function ($query) use ($user) {
                $query->where('administrator_id', $user->id)->orWhere('supervisor_id', $user->id);
            })->get();
        }
        // If Purchasing Officer, fetch all approved requests
        elseif ($user->isRole('purchasing_officer')) {
            $requests = ProcurementRequest::where('status', 'approved')->get();
        } 
        // Default: Fetch all requests (for IT Admin or other roles)
        else {
            $requests = ProcurementRequest::all();
        }

        return view('procurement_requests.index', compact('requests'));
    }

    /**
     * Show the details of a specific procurement request.
     */
    public function show($id)
    {
        $request = ProcurementRequest::findOrFail($id);
        return view('procurement_requests.show', compact('request'));
    }

    /**
     * Supervisor or Administrator Approval
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);
        $requestor = User::findOrFail($procurementRequest->requestor_id);

        // If Supervisor, approve only their Staff requests
        if ($user->isRole('supervisor') && $requestor->supervisor_id == $user->id) {
            $procurementRequest->status = 'approved';
            $procurementRequest->approved_by = $user->id;
            $procurementRequest->save();

            // Store approval record
            Approval::create([
                'request_id' => $procurementRequest->id,
                'approver_id' => $user->id,
                'role' => 'Supervisor',
                'status' => 'approved',
            ]);

            return redirect()->back()->with('success', 'Request approved successfully.');
        }

        // If Administrator, approve Supervisors' and Staff's requests
        if ($user->isRole('admin') && ($requestor->administrator_id == $user->id || $requestor->supervisor_id == $user->id)) {
            $procurementRequest->status = 'approved';
            $procurementRequest->approved_by = $user->id;
            $procurementRequest->save();

            Approval::create([
                'request_id' => $procurementRequest->id,
                'approver_id' => $user->id,
                'role' => 'Administrator',
                'status' => 'approved',
            ]);

            return redirect()->back()->with('success', 'Request approved successfully.');
        }

        return redirect()->back()->with('error', 'Unauthorized to approve this request.');
    }

    /**
     * Reject a Procurement Request
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        if (!$user->canApprove($procurementRequest)) {
            return redirect()->back()->with('error', 'Unauthorized to reject this request.');
        }

        $procurementRequest->status = 'rejected';
        $procurementRequest->remarks = $request->input('remarks');
        $procurementRequest->save();

        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => 'Supervisor/Administrator',
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->back()->with('success', 'Request rejected successfully.');
    }

    /**
     * Purchasing Officer marks as purchased
     */
    public function markAsPurchased($id)
    {
        $user = Auth::user();
        if (!$user->isRole('purchasing_officer')) {
            return redirect()->back()->with('error', 'Only the purchasing officer can perform this action.');
        }

        $procurementRequest = ProcurementRequest::findOrFail($id);
        if ($procurementRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved requests can be marked as purchased.');
        }

        $procurementRequest->status = 'purchased';
        $procurementRequest->save();

        return redirect()->back()->with('success', 'Request marked as purchased.');
    }

    /**
     * Delete a request (IT Admin only)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isRole('it_admin')) {
            return redirect()->back()->with('error', 'Only IT Admin can delete requests.');
        }

        ProcurementRequest::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Request deleted successfully.');
    }
}
