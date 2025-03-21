@extends('layouts.supervisorapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Supervisor Dashboard</h2>

    <!-- Check if there are any requests -->
    @if($requests->isEmpty())
        <p class="text-gray-600 text-lg">No procurement requests found.</p>
    @else
        <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
            <table class="w-full border-collapse border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Requestor</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Office</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Date Requested</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total Items</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $procurementRequest)
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="px-4 py-2 border">{{ $procurementRequest->id }}</td>
                            <td class="px-4 py-2 border">{{ $procurementRequest->requestor->firstname }} {{ $procurementRequest->requestor->lastname }}</td>
                            <td class="px-4 py-2 border">{{ $procurementRequest->office }}</td>
                            <td class="px-4 py-2 border">{{ $procurementRequest->date_requested }}</td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 text-white text-sm font-semibold rounded
                                    {{ $procurementRequest->status == 'pending' ? 'bg-yellow-500' : 'bg-green-500' }}">
                                    {{ ucfirst($procurementRequest->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">{{ $procurementRequest->items->count() }} items</td>
                            <td class="px-4 py-2 border flex space-x-2">
                                <!-- View Button Opens Modal -->
                                <button 
                                    onclick="viewItems({{ $procurementRequest->id }})"
                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    View
                                </button>

                                @if($procurementRequest->status == 'pending')
                                    <form action="{{ route('supervisor.approve', $procurementRequest->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('supervisor.reject', $procurementRequest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                        @csrf
                                        <input type="text" name="remarks" placeholder="Reason for rejection" required class="border p-1 rounded">
                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            Reject
                                        </button>
                                    </form>
                                @endif
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
        <h3 class="text-lg font-semibold mb-4">Procurement Request Items</h3>
        
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
    fetch(`/supervisor/request/${requestId}/items`) // ✅ Fixed URL
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
                            <td class="border px-2 py-1">${item.item_name}</td>
                            <td class="border px-2 py-1">${item.quantity}</td>
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
