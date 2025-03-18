<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\ProcurementItem;
use App\Models\User;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class ProcurementRequestController extends Controller
{
    /**
     * Display the list of procurement requests based on the user's role.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        $requests = match ($user->role) {
            0 => ProcurementRequest::where('requestor_id', $user->id)->with('items', 'requestor')->latest()->paginate(10),
            2 => ProcurementRequest::where('office_id', $user->office_id)->where('status', 'pending')->with('items', 'requestor')->latest()->paginate(10),
            3 => ProcurementRequest::where('status', 'supervisor_approved')->with('items', 'requestor')->latest()->paginate(10),
            4 => ProcurementRequest::where('status', 'admin_approved')->with('items', 'requestor')->latest()->paginate(10),
            1 => ProcurementRequest::where('status', 'comptroller_approved')->with('items', 'requestor')->latest()->paginate(10),
            default => ProcurementRequest::with('items', 'requestor')->latest()->paginate(10),
        };

        return view('staff.index', compact('requests'));
    }

    /**
     * Store a new procurement request along with its items.
     */
    public function store(Request $request)
    {
        $request->validate([
            'office' => 'required|string',
            'date_requested' => 'required|date',
            'items' => 'required|array',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'needs_admin_approval' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        $approvalFlow = $request->has('needs_admin_approval') && $request->needs_admin_approval
            ? 'supervisor_administrator_comptroller_purchasing'
            : 'supervisor_comptroller_purchasing';

        $procurementRequest = ProcurementRequest::create([
            'requestor_id' => $user->id,
            'office_id' => $user->office_id,
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
            'approval_flow' => $approvalFlow,
            'current_approval_stage' => 'supervisor',
        ]);

        foreach ($request->items as $item) {
            ProcurementItem::create([
                'request_id' => $procurementRequest->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => ($item['unit_price'] ?? 0) * $item['quantity'],
                'status' => 'pending',
                'office_id' => $user->office_id,
            ]);
        }

        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => null,
            'role' => 2,
            'status' => 'pending',
            'remarks' => 'Waiting for Supervisor Approval',
        ]);

        return redirect()->route('staff.requests.index')->with('success', 'Procurement request created successfully!');
    }

    /**
     * Approve a Procurement Request based on the user's role.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        if (!$procurementRequest) {
            return redirect()->back()->with('error', 'Procurement request not found.');
        }

        $currentStatus = $procurementRequest->status;
        $approvalFlow = $procurementRequest->approval_flow;

        $statusFlow = [
            'pending' => ['role' => 2, 'next_status' => 'supervisor_approved'],
            'supervisor_approved' => ['role' => 3, 'next_status' => 'admin_approved'],
            'admin_approved' => ['role' => 4, 'next_status' => 'comptroller_approved'],
            'comptroller_approved' => ['role' => 1, 'next_status' => 'ready_for_purchase'],
        ];

        if (!isset($statusFlow[$currentStatus]) || $statusFlow[$currentStatus]['role'] !== $user->role) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }

        $nextStatus = $statusFlow[$currentStatus]['next_status'];

        if ($approvalFlow === 'supervisor_comptroller_purchasing' && $currentStatus === 'supervisor_approved') {
            $nextStatus = 'comptroller_approved';
        }

        $procurementRequest->update([
            'status' => $nextStatus,
            'approved_by' => $user->id,
        ]);

        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => $user->role,
            'status' => 'approved',
            'remarks' => 'Approved by ' . $user->firstname . ' ' . $user->lastname,
        ]);

        return redirect()->back()->with('success', 'Request approved successfully.');
    }

    /**
     * Reject a Procurement Request.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        if (!in_array($user->role, [2, 3, 4])) {
            return redirect()->back()->with('error', 'Unauthorized to reject this request.');
        }

        $procurementRequest->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => $user->role,
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->back()->with('success', 'Request rejected successfully.');
    }

    /**
     * Purchasing Officer marks as purchased.
     */
    public function markAsPurchased($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        if ($user->role != 1 || $procurementRequest->status !== 'comptroller_approved') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $procurementRequest->update(['status' => 'purchased']);

        return redirect()->back()->with('success', 'Request marked as purchased.');
    }
    public function create()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'You must be logged in.');
    }

    // Fetch existing items that belong to the user's office
    $existingItems = ProcurementItem::where('office_id', $user->office_id)
        ->select('item_name', DB::raw('MAX(unit_price) as unit_price'))
        ->groupBy('item_name')
        ->paginate(10); // ✅ Added pagination

    return view('staff.create', compact('user', 'existingItems'));
}

}
