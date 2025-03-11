<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementItem;
use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem; // Ensure this exists to store request items

class StaffController extends Controller
{
    public function dashboard()
    {
        // ✅ Use pagination to fix `$requests->links()` issue
        $requests = ProcurementRequest::where('requestor_id', auth()->id())
                    ->with('items')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10); // ✅ Fix: Use paginate()

        return view('staff.dashboard', compact('requests'));
    }

    public function create()
    {
        // ✅ Ensure `office_id` exists before filtering items
        $existingItems = ProcurementItem::when(auth()->user()->office_id, function ($query) {
                            return $query->where('office_id', auth()->user()->office_id);
                        })
                        ->paginate(10);

        return view('staff.create', compact('existingItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'office' => 'required|string',
            'date_requested' => 'required|date',
            'items' => 'required|array', // ✅ Ensure items are selected
            'items.*.id' => 'required|exists:procurement_items,id', // ✅ Ensure valid item IDs
            'items.*.quantity' => 'required|integer|min:1', // ✅ Validate quantity
        ]);

        // ✅ Create a new procurement request
        $procurementRequest = ProcurementRequest::create([
            'requestor_id' => auth()->id(),
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
        ]);

        // ✅ Save selected items in `procurement_request_items`
        foreach ($request->items as $item) {
            ProcurementRequestItem::create([
                'procurement_request_id' => $procurementRequest->id,
                'procurement_item_id' => $item['id'],
                'item_name' => ProcurementItem::find($item['id'])->item_name, // ✅ Store item name
                'unit_price' => ProcurementItem::find($item['id'])->unit_price, // ✅ Store current price
                'quantity' => $item['quantity'],
                'total_price' => ProcurementItem::find($item['id'])->unit_price * $item['quantity'],
            ]);
        }

        return redirect()->route('staff.dashboard')->with('success', 'Request submitted successfully!');
    }

    public function edit($id)
    {
        $request = ProcurementRequest::where('id', $id)
                    ->where('requestor_id', auth()->id())
                    ->firstOrFail();

        if ($request->status !== 'pending') {
            return redirect()->route('staff.dashboard')->with('error', 'Cannot edit an approved request.');
        }

        return view('staff.edit_request', compact('request'));
    }

    public function update(Request $request, $id)
    {
        $requestData = ProcurementRequest::where('id', $id)
                        ->where('requestor_id', auth()->id())
                        ->firstOrFail();

        if ($requestData->status !== 'pending') {
            return redirect()->route('staff.dashboard')->with('error', 'Cannot update an approved request.');
        }

        $request->validate([
            'office' => 'required|string',
            'date_requested' => 'required|date',
        ]);

        $requestData->update([
            'office' => $request->office,
            'date_requested' => $request->date_requested,
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Request updated successfully.');
    }
}
