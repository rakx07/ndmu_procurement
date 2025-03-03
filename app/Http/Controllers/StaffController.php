<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel; // Ensure this matches your model

class StaffController extends Controller
{
    public function dashboard()
    {
        $requests = RequestModel::where('requestor_id', auth()->id())->get();
        return view('staff.dashboard', compact('requests'));
    }

    public function create()
    {
        return view('staff.create_request');
    }

    public function store(Request $request)
    {
        $request->validate([
            'office' => 'required|string',
            'date_requested' => 'required|date',
        ]);

        RequestModel::create([
            'requestor_id' => auth()->id(),
            'office' => $request->office,
            'date_requested' => $request->date_requested,
            'status' => 'pending',
            'remarks' => null,
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Request submitted.');
    }

    public function edit($id)
    {
        $request = RequestModel::where('id', $id)->where('requestor_id', auth()->id())->firstOrFail();

        if ($request->status !== 'pending') {
            return redirect()->route('staff.dashboard')->with('error', 'Cannot edit an approved request.');
        }

        return view('staff.edit_request', compact('request'));
    }

    public function update(Request $request, $id)
    {
        $requestData = RequestModel::where('id', $id)->where('requestor_id', auth()->id())->firstOrFail();

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

        return redirect()->route('staff.dashboard')->with('success', 'Request updated.');
    }
}
