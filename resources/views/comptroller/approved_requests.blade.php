@extends('layouts.comptrollerapp')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Approved Procurement Requests</h1>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
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
                <th class="px-4 py-2 border">Approval Date</th>
                <th class="px-4 py-2 border">Actions</th> <!-- ✅ Added actions column -->
            </tr>
        </thead>
        <tbody>
            @forelse ($approvedRequests as $key => $request)
                <tr class="border">
                    <td class="px-4 py-2 border">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $request->id }}</td>
                    <td class="px-4 py-2 border">
                        {{ optional($request->requestor)->full_name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-2 border">{{ $request->office }}</td>
                    <td class="px-4 py-2 border">{{ $request->item_description ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $request->updated_at->format('F d, Y') }}</td>
                    <td class="px-4 py-2 border">
                        <button onclick="openModal({{ $request->id }})"
                            class="bg-blue-500 text-white px-4 py-1 rounded">
                            View Items
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No approved requests.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal for Viewing Items -->
<div id="itemModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-xl font-bold mb-4">Requested Items</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Item Name</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Unit Price</th>
                    <th class="px-4 py-2 border">Total Price</th>
                </tr>
            </thead>
            <tbody id="modalItems">
                <!-- Items will be populated here dynamically -->
            </tbody>
        </table>
        <div class="flex justify-end mt-4">
            <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-1 rounded">Close</button>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Modal -->
<script>
    let requestData = @json($approvedRequests);

    function openModal(requestId) {
        let modal = document.getElementById('itemModal');
        let modalItems = document.getElementById('modalItems');
        modalItems.innerHTML = '';

        let request = requestData.find(r => r.id === requestId);

        if (request && request.items && request.items.length > 0) {
            request.items.forEach((item, index) => {
                modalItems.innerHTML += `
                    <tr class="border">
                        <td class="px-4 py-2 border">${index + 1}</td>
                        <td class="px-4 py-2 border">${item.item_name ?? 'N/A'}</td> <!-- ✅ Fixed property -->
                        <td class="px-4 py-2 border">${item.quantity ?? 'N/A'}</td>
                        <td class="px-4 py-2 border">${item.unit_price ?? 'N/A'}</td>
                        <td class="px-4 py-2 border">${item.total_price ?? 'N/A'}</td>
                    </tr>
                `;
            });
        } else {
            modalItems.innerHTML = `
                <tr><td colspan="5" class="text-center py-4">No items found.</td></tr>
            `;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        let modal = document.getElementById('itemModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
