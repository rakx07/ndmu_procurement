@extends('layouts.administratorapp')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Rejected Procurement Requests</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-200 text-sm">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Request ID</th>
                    <th class="px-4 py-2 border">Requested By</th>
                    <th class="px-4 py-2 border">Department</th>
                    <th class="px-4 py-2 border">Item Description</th>
                    <th class="px-4 py-2 border">Remarks</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rejectedRequests as $key => $request)
                    <tr class="border">
                        <td class="px-4 py-2 border">{{ $key + 1 }}</td>
                        <td class="px-4 py-2 border">{{ $request->id }}</td>
                        <td class="px-4 py-2 border">{{ $request->requestor->full_name ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border">{{ $request->office }}</td>
                        <td class="px-4 py-2 border">{{ $request->item_description ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border text-red-600 italic">
                            {{ $request->remarks ?? 'No remarks' }}
                        </td>
                        <td class="px-4 py-2 border">
                            <span class="text-red-600 font-semibold">Rejected</span>
                        </td>
                        <td class="px-4 py-2 border">
                            <button 
                                onclick="viewItems({{ $request->id }})"
                                class="bg-blue-500 text-white text-sm px-2 py-1 rounded hover:bg-blue-600">
                                View Items
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">No rejected requests.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ✅ Modal for Viewing Items -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-4xl max-h-[80vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Procurement Request Items</h3>
        <table class="min-w-full table-auto border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Item Name</th>
                    <th class="border px-4 py-2">Quantity</th>
                    <th class="border px-4 py-2">Unit Price</th>
                    <th class="border px-4 py-2">Total Price</th>
                </tr>
            </thead>
            <tbody id="modal-items-container"></tbody>
        </table>
        <div class="text-right mt-4">
            <button onclick="hideModal()" class="bg-red-500 text-white px-4 py-2 text-sm rounded hover:bg-red-600">
                Close
            </button>
        </div>
    </div>
</div>

<!-- ✅ JavaScript -->
<script>
function viewItems(requestId) {
    fetch(`/admin/requests/${requestId}/items`)
        .then(response => {
            if (!response.ok) throw new Error('Request failed');
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('modal-items-container');
            container.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500 py-4">No items found</td></tr>';
            } else {
                data.forEach(item => {
                    container.innerHTML += `
                        <tr>
                            <td class="border px-4 py-2">${item.item_name}</td>
                            <td class="border px-4 py-2">${item.quantity}</td>
                            <td class="border px-4 py-2">${parseFloat(item.unit_price || 0).toFixed(2)}</td>
                            <td class="border px-4 py-2">${parseFloat(item.total_price || 0).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }

            document.getElementById('viewModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modal-items-container').innerHTML = `
                <tr><td colspan="4" class="text-center text-red-500 py-4">Failed to load items.</td></tr>
            `;
            document.getElementById('viewModal').classList.remove('hidden');
        });
}

function hideModal() {
    document.getElementById('viewModal').classList.add('hidden');
}
</script>
@endsection
