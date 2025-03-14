<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementItem;
use App\Models\ProcurementRequest;
use App\Models\ItemCategory; // ✅ Ensure ItemCategory is imported

class PurchasingOfficerController extends Controller
{
    public function dashboard()
    {
        return view('purchasing_officer.dashboard', [
            'totalRequests' => ProcurementRequest::count(),
            'pendingRequests' => ProcurementRequest::where('status', 'pending')->count(),
            'approvedRequests' => ProcurementRequest::where('status', 'approved')->count(),
            'procurementRequests' => ProcurementRequest::latest()->get(),
            'procurementItems' => ProcurementItem::latest()->get(),
        ]);
    }

    public function create()
    {
        // Fetch all categories and procurement items from the database
        $categories = ItemCategory::all();
        $procurementItems = ProcurementItem::latest()->get(); // ✅ Load procurement items here

        return view('purchasing_officer.create', compact('categories', 'procurementItems'));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'item_name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1', // ✅ Correct validation
            'supplier_name' => 'nullable|string',
            'item_category_id' => 'required|exists:item_categories,id',
        ]);

        // ✅ Set default quantity to 1 if it's null
        $quantity = $request->quantity ?? 1;

        // ✅ Calculate total price
        $total_price = $quantity * $request->unit_price;

        // ✅ Store item in database
        ProcurementItem::create([
            'item_name' => $request->item_name,
            'unit_price' => $request->unit_price,
            'quantity' => $quantity,
            'total_price' => $total_price, 
            'supplier_name' => $request->supplier_name,
            'item_category_id' => $request->item_category_id,
        ]);

        // ✅ Redirect back to the create page with success message
        return redirect()->route('purchasing_officer.items.create')->with('success', 'Item added successfully!');
    }

    public function destroy($id)
    {
        $item = ProcurementItem::findOrFail($id);
        $item->delete();

        return redirect()->route('purchasing_officer.items.create')->with('success', 'Item deleted successfully!');
    }
}
