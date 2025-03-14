<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementItem;
use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function dashboard()
    {
        $requests = ProcurementRequest::where('requestor_id', auth()->id())
                    ->with('items')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('staff.dashboard', compact('requests'));
    }

    public function create()
            {
                // Fetch all procurement items from the database
                $existingItems = ProcurementItem::orderBy('item_name', 'asc')->paginate(10);

                // Log the retrieved items for debugging
                if ($existingItems->isEmpty()) {
                    \Log::warning('No procurement items found in the database.');
                } else {
                    \Log::info('Procurement Items Retrieved:', $existingItems->toArray());
                }

                return view('staff.create', compact('existingItems'));
            }


    public function store(Request $request)
    {
        $request->validate([
            'office' => 'required|string',
            'date_requested' => 'required|date',
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ✅ Create a new procurement request
        $procurementRequest = ProcurementRequest::create([
            'requestor_id' => auth()->id(),
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
            'requires_admin_approval' => $request->has('requires_admin_approval'),
        ]);

        // ✅ Save selected items
        foreach ($request->items as $key => $item) {
            $procurementItem = ProcurementItem::find($item['id']) ?? null;
            $imagePath = null;

            // ✅ Handle file upload if exists
            if ($request->hasFile("items.$key.image")) {
                $imagePath = $request->file("items.$key.image")->store('procurement_references', 'public');
            }

            ProcurementRequestItem::create([
                'procurement_request_id' => $procurementRequest->id,
                'procurement_item_id' => $procurementItem ? $procurementItem->id : null,
                'item_name' => $procurementItem ? $procurementItem->item_name : ($item['item_name'] ?? 'Unknown Item'),
                'unit_price' => $procurementItem ? $procurementItem->unit_price : ($item['unit_price'] ?? 0),
                'quantity' => $item['quantity'],
                'total_price' => ($procurementItem ? $procurementItem->unit_price : ($item['unit_price'] ?? 0)) * $item['quantity'],
                'image_path' => $imagePath,
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
