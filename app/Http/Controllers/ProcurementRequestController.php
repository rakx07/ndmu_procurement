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

    // Fetch procurement requests based on role
    if ($user->role == 0) { // Staff - Can only see their own office's requests
        $requests = ProcurementRequest::where('office_id', $user->office_id)->with('requestor')->get();
    } elseif ($user->role == 2) { // Supervisor - Sees pending requests
        $requests = ProcurementRequest::where('status', 'pending')->with('requestor')->get();
    } elseif ($user->role == 3) { // Administrator - Sees supervisor-approved requests
        $requests = ProcurementRequest::where('status', 'supervisor_approved')->with('requestor')->get();
    } elseif ($user->role == 4) { // Comptroller - Sees admin-approved requests
        $requests = ProcurementRequest::where('status', 'admin_approved')->with('requestor')->get();
    } elseif ($user->role == 1) { // Purchasing Officer - Sees comptroller-approved requests
        $requests = ProcurementRequest::where('status', 'comptroller_approved')->with('requestor')->get();
    } else { // IT Admin and others see all requests
        $requests = ProcurementRequest::with('requestor')->get();
    }

    return view('staff.index', compact('requests')); // ✅ Ensures staff see only their office's requests
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

        // Determine next approval step based on role
        if ($user->role == 2 && $procurementRequest->status == 'pending') { // Supervisor Approval
            $procurementRequest->status = 'supervisor_approved';
        } elseif ($user->role == 3 && $procurementRequest->status == 'supervisor_approved') { // Admin Approval
            $procurementRequest->status = 'admin_approved';
        } elseif ($user->role == 4 && $procurementRequest->status == 'admin_approved') { // Comptroller Approval
            $procurementRequest->status = 'comptroller_approved';
        } else {
            return redirect()->back()->with('error', 'You are not authorized to approve this request.');
        }

        // Save approval and log it
        $procurementRequest->approved_by = $user->id;
        $procurementRequest->save();

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

        // Mark request as rejected
        $procurementRequest->status = 'rejected';
        $procurementRequest->remarks = $request->input('remarks');
        $procurementRequest->save();

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

        if ($user->role != 1) { // Only Purchasing Officer
            return redirect()->back()->with('error', 'Only the purchasing officer can perform this action.');
        }

        if ($procurementRequest->status !== 'comptroller_approved') {
            return redirect()->back()->with('error', 'Only fully approved requests can be marked as purchased.');
        }

        $procurementRequest->status = 'purchased';
        $procurementRequest->save();

        return redirect()->back()->with('success', 'Request marked as purchased.');
    }

    /**
     * Delete a Procurement Request (Only IT Admin).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role != 5) { // IT Admin only
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
        $user = auth()->user();
    
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }
    
        // Fetch distinct items for the user's office
        $existingItems = ProcurementItem::where('office_id', $user->office_id)
                        ->select('id', 'item_name', 'unit_price', 'office_id')
                        ->distinct()
                        ->get();
    
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

        $user = auth()->user();

        // Create procurement request
        $procurementRequest = ProcurementRequest::create([
            'requestor_id' => $user->id,
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
        ]);

        // Add items to procurement request
        foreach ($request->items as $item) {
            ProcurementItem::create([
                'request_id' => $procurementRequest->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'] ?? null,
                'total_price' => isset($item['unit_price']) ? $item['unit_price'] * $item['quantity'] : null,
                'status' => 'pending',
                'office_id' => $user->office_id, // ✅ Ensure office ID is stored
            ]);
        }

        return redirect()->route('staff.requests.index')->with('success', 'Procurement request created successfully!');
    }
}
