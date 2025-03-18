@extends('layouts.supervisorapp')

@section('content')
<div class="container mx-auto mt-6 p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Rejected Procurement Requests</h2>

    @if($requests->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-900 text-white text-sm uppercase">
                        <th class="py-3 px-6 text-left border-r">Request ID</th>
                        <th class="py-3 px-6 text-left border-r">Office</th>
                        <th class="py-3 px-6 text-left border-r">Date Requested</th>
                        <th class="py-3 px-6 text-left border-r">Remarks</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach($requests as $request)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-6 border-r">{{ $request->id }}</td>
                            <td class="py-3 px-6 border-r">{{ $request->office }}</td>
                            <td class="py-3 px-6 border-r">{{ $request->date_requested }}</td>
                            <td class="py-3 px-6 border-r text-red-500">{{ $request->remarks ?? 'N/A' }}</td>
                            <td class="py-3 px-6">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-200" 
                                        onclick="viewRejectedItems({{ $request->id }})">
                                    View Items
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @else
        <p class="text-gray-500 text-lg">No rejected requests found.</p>
    @endif
</div>

<!-- Modal for viewing rejected items -->
<div id="rejected-items-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 shadow-lg w-96">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Rejected Request Items</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeRejectedItemsModal()">✖</button>
        </div>
        <div id="rejected-items-modal-content" class="mt-4 text-gray-700">
            <!-- Items will be inserted here dynamically -->
        </div>
        <div class="mt-6 flex justify-end">
            <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200" 
                    onclick="closeRejectedItemsModal()">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function viewRejectedItems(requestId) {
        fetch(`/supervisor/rejected-requests/${requestId}/items`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert("Unauthorized access.");
                    return;
                }

                let itemsList = "<ul class='list-disc ml-6 space-y-2'>";
                data.forEach(item => {
                    itemsList += `<li><strong>${item.item_name}</strong> - ${item.quantity} pcs @ ₱${item.unit_price}</li>`;
                });
                itemsList += "</ul>";

                document.getElementById('rejected-items-modal-content').innerHTML = itemsList;
                document.getElementById('rejected-items-modal').classList.remove('hidden');
            })
            .catch(error => console.error('Error:', error));
    }

    function closeRejectedItemsModal() {
        document.getElementById('rejected-items-modal').classList.add('hidden');
    }
</script>

@endsection
