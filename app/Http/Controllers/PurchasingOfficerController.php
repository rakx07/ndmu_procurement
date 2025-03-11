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
        // Validate the input fields
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);
    
        // Save new item
        ProcurementItem::create([
            'item_name' => $request->input('item_name'),
            'quantity' => $request->input('quantity'),
        ]);
    
        return redirect()->route('purchasing_officer.dashboard')->with('success', 'Item added successfully!');
    }
    
}
