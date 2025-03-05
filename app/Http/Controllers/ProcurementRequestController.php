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
            0 => ProcurementRequest::where('office_id', $user->office_id)->with('requestor')->get(),
            2 => ProcurementRequest::where('status', 'pending')->with('requestor')->get(),
            3 => ProcurementRequest::where('status', 'supervisor_approved')->with('requestor')->get(),
            4 => ProcurementRequest::where('status', 'admin_approved')->with('requestor')->get(),
            1 => ProcurementRequest::where('status', 'comptroller_approved')->with('requestor')->get(),
            default => ProcurementRequest::with('requestor')->get(),
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
        $user = Auth::user(); // ✅ Fixed method call
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

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
                'unit_price' => $item['unit_price'] ?? null,
                'total_price' => isset($item['unit_price']) ? $item['unit_price'] * $item['quantity'] : null,
                'status' => 'pending',
                'office_id' => $user->office_id,
            ]);
        }

        return redirect()->route('staff.requests.index')->with('success', 'Procurement request created successfully!');
    }
}
