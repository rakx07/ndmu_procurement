<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem;
use App\Models\Approval;
use App\Models\ProcurementItem;
use Illuminate\Support\Facades\DB;


class ProcurementRequestController extends Controller
{
    /**
     * Display procurement requests based on user roles.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        // Fetch procurement requests based on user role
        $requests = match ($user->role) {
            0 => ProcurementRequest::where('requestor_id', $user->id)->with('items')->latest()->paginate(10),
            2 => ProcurementRequest::where('office_id', $user->office_id)->where('status', 'pending')->with('items')->latest()->paginate(10),
            3 => ProcurementRequest::where('status', 'supervisor_approved')->with('items')->latest()->paginate(10),
            4 => ProcurementRequest::where('status', 'admin_approved')->with('items')->latest()->paginate(10),
            1 => ProcurementRequest::where('status', 'comptroller_approved')->with('items')->latest()->paginate(10),
            default => ProcurementRequest::with('items')->latest()->paginate(10),
        };

        return redirect()->route('staff.dashboard')->with('success', 'Procurement request submitted successfully!');

    }

    /**
     * Store a new procurement request along with its items.
     */
    public function store(Request $request)
{
    $request->validate([
        'office' => 'required|string',
        'date_requested' => 'required|date',
        'needs_admin_approval' => 'nullable|boolean', // Allow checkbox value
        'items' => 'required|array',
        'items.*.item_name' => 'required|string',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();

    DB::transaction(function () use ($user, $request) {
        $procurementRequest = ProcurementRequest::create([
            'requestor_id' => $user->id,
            'office_id' => $user->office_id,
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
            'needs_admin_approval' => $request->has('needs_admin_approval'), // Capture checkbox
        ]);

        foreach ($request->items as $item) {
            ProcurementRequestItem::create([
                'procurement_request_id' => $procurementRequest->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => ($item['unit_price'] ?? 0) * $item['quantity'],
            ]);
        }
    });

    return redirect()->route('staff.requests.index')->with('success', 'Procurement request created successfully!');
}

    

    /**
     * Approve a procurement request based on user role.
     */
    public function approve($id)
{
    $user = Auth::user();
    $procurementRequest = ProcurementRequest::findOrFail($id);

    $currentStatus = $procurementRequest->status;

    $statusFlow = [
        'pending' => ['role' => 2, 'next_status' => 'supervisor_approved'],
        'supervisor_approved' => [
            'role' => 3, 
            'next_status' => $procurementRequest->needs_admin_approval ? 'admin_approved' : 'comptroller_approved'
        ],
        'admin_approved' => ['role' => 4, 'next_status' => 'comptroller_approved'],
        'comptroller_approved' => ['role' => 1, 'next_status' => 'ready_for_purchase'],
    ];

    if (!isset($statusFlow[$currentStatus]) || $statusFlow[$currentStatus]['role'] !== $user->role) {
        return redirect()->back()->with('error', 'You are not authorized to approve this request.');
    }

    $nextStatus = $statusFlow[$currentStatus]['next_status'];

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
     * Reject a procurement request.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

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
    public function create()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'You must be logged in.');
    }

    // ✅ Ensure only available items are shown
    $existingItems = ProcurementItem::where('status', 'available')
        ->select('id', 'item_name', 'supplier_name', 'unit_price')
        ->orderBy('item_name', 'asc')
        ->paginate(10);

    return view('staff.create', compact('user', 'existingItems'));
}

}
