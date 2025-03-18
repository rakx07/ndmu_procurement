@extends('layouts.administratorapp')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Approved Requests</h1>

    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Request ID</th>
                <th class="px-4 py-2 border">Requested By</th>
                <th class="px-4 py-2 border">Department</th>
                <th class="px-4 py-2 border">Item Description</th>
                <th class="px-4 py-2 border">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($approvedRequests as $key => $request)
                <tr class="border">
                    <td class="px-4 py-2 border">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $request->id }}</td>
                    <td class="px-4 py-2 border">{{ $request->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $request->office }}</td>
                    <td class="px-4 py-2 border">{{ $request->item_description ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border text-green-500 font-bold">Approved</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No approved requests.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
