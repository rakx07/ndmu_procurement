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

    // ✅ Fetch procurement requests based on the user role
    $requests = match ($user->role) {
        // Staff: Only see their own requests
        0 => ProcurementRequest::where('requestor_id', $user->id)
            ->with('items', 'requestor')
            ->latest()
            ->paginate(10),

        // Supervisor: See all pending requests from their office
        2 => ProcurementRequest::where('office_id', $user->office_id)
            ->where('status', 'pending')
            ->with('items', 'requestor')
            ->latest()
            ->paginate(10),

        // Administrator: See requests that have been approved by the supervisor
        3 => ProcurementRequest::where('status', 'supervisor_approved')
            ->with('items', 'requestor')
            ->latest()
            ->paginate(10),

        // Comptroller: See requests that have been approved by the administrator
        4 => ProcurementRequest::where('status', 'admin_approved')
            ->with('items', 'requestor')
            ->latest()
            ->paginate(10),

        // Purchasing Officer: See requests ready for purchase
        1 => ProcurementRequest::where('status', 'comptroller_approved')
            ->with('items', 'requestor')
            ->latest()
            ->paginate(10),

        // IT Admin: See all requests
        default => ProcurementRequest::with('items', 'requestor')
            ->latest()
            ->paginate(10),
    };

    return view('staff.index', compact('requests'));
}


    /**
     * Show the details of a specific procurement request.
     */
    public function show($id)
    {
        $request = ProcurementRequest::with('items')->findOrFail($id);
        return view('procurement_requests.show', compact('request'));
    }

    /**
     * Approve a Procurement Request based on the user's role.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $procurementRequest = ProcurementRequest::findOrFail($id);

        $statusFlow = [
            'pending' => [2, 'supervisor_approved'],
            'supervisor_approved' => [3, 'admin_approved'],
            'admin_approved' => [4, 'comptroller_approved'],
        ];

        if (!isset($statusFlow[$procurementRequest->status]) || $statusFlow[$procurementRequest->status][0] !== $user->role) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }

        $procurementRequest->update([
            'status' => $statusFlow[$procurementRequest->status][1],
            'approved_by' => $user->id,
        ]);

        Approval::create([
            'request_id' => $procurementRequest->id,
            'approver_id' => $user->id,
            'role' => $user->role,
            'status' => 'approved',
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

    /**
     * Delete a Procurement Request (Only IT Admin).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role != 5) {
            return redirect()->back()->with('error', 'Only IT Admin can delete requests.');
        }

        ProcurementRequest::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Request deleted successfully.');
    }

    /**
     * Show the create form for procurement requests.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        // Get distinct items based on item_name and office_id, keeping the latest unit_price
        $existingItems = ProcurementItem::where('office_id', $user->office_id)
            ->select('item_name', DB::raw('MAX(unit_price) as unit_price'))
            ->groupBy('item_name')
            ->paginate(10); // ✅ Added pagination

        return view('staff.create', compact('user', 'existingItems'));
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
        ]);

        $user = Auth::user();

        $procurementRequest = ProcurementRequest::create([
            'requestor_id' => $user->id,
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
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

        return redirect()->route('staff.requests.index')->with('success', 'Procurement request created successfully!');
    }

    /**
     * Add new item to the procurement system.
     */
    public function addItem(Request $request)
    {
        try {
            $request->validate([
                'item_name' => 'required|string',
                'unit_price' => 'required|numeric|min:0',
            ]);

            $user = Auth::user();

            $item = ProcurementItem::create([
                'request_id' => null, // Not linked to a request yet
                'item_name' => $request->item_name,
                'quantity' => 1, // Default quantity
                'unit_price' => $request->unit_price,
                'total_price' => $request->unit_price,
                'status' => 'available',
                'office_id' => $user->office_id ?? null,
            ]);

            return response()->json(['success' => true, 'item' => $item], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get request items for a specific procurement request.
     */
    public function getRequestItems($id)
    {
        $items = ProcurementItem::where('request_id', $id)
            ->select('item_name', 'quantity', 'unit_price', 'total_price')
            ->get();

        return response()->json($items);
    }
}
