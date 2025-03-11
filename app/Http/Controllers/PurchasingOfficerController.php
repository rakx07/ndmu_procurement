<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementItem; // Use the correct model
use App\Models\ProcurementRequest; // Use the correct model

class PurchasingOfficerController extends Controller
{
    //
    public function dashboard()
{
    return view('purchasing_officer.dashboard', [
        'totalRequests' => ProcurementRequest::count(),
        'pendingRequests' => ProcurementRequest::where('status', 'pending')->count(),
        'approvedRequests' => ProcurementRequest::where('status', 'approved')->count(),
        'procurementRequests' => ProcurementRequest::latest()->get(),
        'procurementItems' => ProcurementItem::latest()->get(), // ✅ Add this
    ]);
}

public function create()
{
    // Fetch all categories and procurement items from the database
    $categories = \App\Models\ItemCategory::all();
    $procurementItems = \App\Models\ProcurementItem::latest()->get();

    // Pass them to the view
    return view('purchasing_officer.create', compact('categories', 'procurementItems'));
}


public function store(Request $request)
{
    // Validate input
    $request->validate([
        'item_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'unit_price' => 'required|numeric|min:0',
        'supplier_name' => 'nullable|string',
        'item_category_id' => 'required|exists:item_categories,id',
    ]);

    // ✅ Calculate total price
    $total_price = $request->quantity * $request->unit_price;

    // ✅ Store item in database
    ProcurementItem::create([
        'item_name' => $request->item_name,
        'quantity' => $request->quantity,
        'unit_price' => $request->unit_price,
        'total_price' => $total_price, // ✅ Save total price
        'supplier_name' => $request->supplier_name,
        'item_category_id' => $request->item_category_id,
    ]);

    return redirect()->route('purchasing_officer.dashboard')->with('success', 'Item added successfully!');
}


}
