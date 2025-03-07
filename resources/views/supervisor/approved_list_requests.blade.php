@extends('layouts.supervisorapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Approved Procurement Requests</h2>

    <!-- Check if there are any approved requests -->
    @if($approvedRequests->isEmpty())
        <p class="text-gray-600 text-lg">No approved procurement requests found.</p>
    @else
        <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
            <table class="w-full border-collapse border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Request ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Requestor</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Remarks</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Approved Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedRequests as $request)
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="px-4 py-2 border">{{ $request->request_id }}</td>
                            <td class="px-4 py-2 border">{{ $request->requestor->firstname }} {{ $request->requestor->lastname }}</td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 text-white text-sm font-semibold rounded bg-green-500">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $request->remarks ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 border">{{ $request->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
