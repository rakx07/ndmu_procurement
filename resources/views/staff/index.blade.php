@extends('layouts.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Your Procurement Requests</h2>

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Requestor</th>
                <th class="border px-4 py-2">Office</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Total Items</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td class="border px-4 py-2">{{ $request->id }}</td>
                    <td class="border px-4 py-2">{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}</td>
                    <td class="border px-4 py-2">{{ $request->office }}</td>
                    <td class="border px-4 py-2">{{ ucfirst($request->status) }}</td>
                    <td class="border px-4 py-2">
                        {{ $request->items->count() }} items
                    </td>
                    <td class="border px-4 py-2">
                        <button onclick="viewItems({{ $request->id }})" class="text-blue-500">View</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 p-4">No procurement requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ✅ View Modal -->
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
    fetch(`/staff/request/${requestId}/items`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            let container = document.getElementById('modal-items-container');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td class="border px-2 py-1">${item.item_name}</td>
                            <td class="border px-2 py-1">${item.quantity}</td>
                            <td class="border px-2 py-1">${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td class="border px-2 py-1">${parseFloat(item.total_price).toFixed(2)}</td>
                        </tr>
                    `;
                    container.insertAdjacentHTML('beforeend', row);
                });
            }

            document.getElementById('viewModal').classList.remove('hidden');
        })
        .catch(error => console.error('Error fetching items:', error));
}

function hideModal() {
    document.getElementById('viewModal').classList.add('hidden');
}
</script>
@endsection
