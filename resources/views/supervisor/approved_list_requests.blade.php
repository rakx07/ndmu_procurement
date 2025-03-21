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
                        <th class="border border-gray-300 px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedRequests as $procurementRequest)
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="px-4 py-2 border">{{ $procurementRequest->office_req_id }}</td>
                            <td class="px-4 py-2 border">
                                {{ $procurementRequest->requestor->firstname ?? 'N/A' }} 
                                {{ $procurementRequest->requestor->lastname ?? '' }}
                            </td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 text-white text-sm font-semibold rounded bg-green-500">
                                    {{ ucfirst($procurementRequest->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $procurementRequest->remarks ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 border">{{ $procurementRequest->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-2 border">
                                <button onclick="viewItems({{ $procurementRequest->office_req_id }})" 
                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    View Items
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- ✅ View Modal for Procurement Items -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-1/3">
        <h3 class="text-lg font-semibold mb-4">Approved Procurement Request Items</h3>
        
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">Item Name</th>
                    <th class="border px-2 py-1">Quantity</th>
                    <th class="border px-2 py-1">Unit Price</th>
                    <th class="border px-2 py-1">Total Price</th>
                </tr>
            </thead>
            <tbody id="modal-items-container"></tbody>
        </table>

        <button onclick="hideModal()" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Close</button>
    </div>
</div>

<script>
function viewItems(requestId) {
    fetch(`/supervisor/approved-request/${requestId}/items`)  // ✅ Fixed fetch URL
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            let container = document.getElementById('modal-items-container');
            container.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td class="border px-2 py-1">${item.item_name ?? 'N/A'}</td>
                            <td class="border px-2 py-1">${item.quantity ?? 0}</td>
                            <td class="border px-2 py-1">${parseFloat(item.unit_price || 0).toFixed(2)}</td>
                            <td class="border px-2 py-1">${parseFloat(item.total_price || 0).toFixed(2)}</td>
                        </tr>
                    `;
                    container.insertAdjacentHTML('beforeend', row);
                });
            }

            document.getElementById('viewModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching items:', error);
            document.getElementById('modal-items-container').innerHTML = `
                <tr><td colspan="4" class="text-center text-red-500">Failed to load items.</td></tr>
            `;
        });
}

function hideModal() {
    document.getElementById('viewModal').classList.add('hidden');
}
</script>
@endsection
