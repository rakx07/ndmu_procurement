<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementItem;
use App\Models\ProcurementRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PurchasingOfficerController extends Controller
{
    public function dashboard()
    {
        return view('purchasing_officer.dashboard', [
            'totalRequests' => ProcurementRequest::count(),
            'pendingRequests' => ProcurementRequest::where('status', 'pending')->count(),
           'approvedRequests' => ProcurementRequest::where('status', 'comptroller_approved')->count(),
            'procurementRequests' => ProcurementRequest::with(['requestor', 'approvals.approver'])->latest()->get(),
            'procurementItems' => ProcurementItem::latest()->get(),
        ]);
    }

    public function create()
    {
        $categories = \App\Models\ItemCategory::all();
        $procurementItems = ProcurementItem::latest()->get();

        return view('purchasing_officer.create', compact('categories', 'procurementItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'supplier_name' => 'nullable|string',
            'item_category_id' => 'required|exists:item_categories,id',
        ]);

        $quantity = $request->quantity ?? 1;
        $total_price = $quantity * $request->unit_price;

        ProcurementItem::create([
            'item_name' => $request->item_name,
            'unit_price' => $request->unit_price,
            'quantity' => $quantity,
            'total_price' => $total_price,
            'supplier_name' => $request->supplier_name,
            'item_category_id' => $request->item_category_id,
        ]);

        return redirect()->route('purchasing_officer.create')->with('success', 'Item added successfully!');
    }

    public function destroy($id)
    {
        $item = ProcurementItem::findOrFail($id);
        $item->delete();

        return redirect()->route('purchasing_officer.create')->with('success', 'Item deleted successfully!');
    }

    public function getItems($id)
    {
        $request = ProcurementRequest::with('items')->findOrFail($id);
        return response()->json($request->items);
    }

    //Printing Options
    public function print($id)
{
    $request = ProcurementRequest::with(['items', 'approvals.approver'])->findOrFail($id);
    $comptrollerApproval = $request->approvals->firstWhere('approver.role', 4);

    return Pdf::loadView('purchasing_officer.pdf.report', [
        'request' => $request,
        'comptrollerName' => $comptrollerApproval?->approver?->full_name ?? 'N/A',
    ])->stream("Request_Report_{$id}.pdf");
}

}
