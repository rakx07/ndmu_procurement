@extends('layouts.comptrollerapp')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Rejected Procurement Requests</h1>

    @if(session('success'))
        <div class="bg-red-200 text-red-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Request ID</th>
                <th class="px-4 py-2 border">Requested By</th>
                <th class="px-4 py-2 border">Department</th>
                <th class="px-4 py-2 border">Item Description</th>
                <th class="px-4 py-2 border">Rejection Date</th>
                <th class="px-4 py-2 border">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rejectedRequests as $key => $request)
                <tr class="border">
                    <td class="px-4 py-2 border">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $request->id }}</td>
                    <td class="px-4 py-2 border">{{ $request->requestor->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $request->office }}</td>
                    <td class="px-4 py-2 border">{{ $request->item_description ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $request->updated_at->format('F d, Y') }}</td>
                    <td class="px-4 py-2 border text-red-600">{{ $request->remarks ?? 'No remarks' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No rejected requests.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
