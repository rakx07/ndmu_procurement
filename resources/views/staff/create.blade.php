@extends('layouts.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Create Procurement Request</h2>

    <div class="grid grid-cols-2 gap-6">
        <!-- Left Side: Request Form -->
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">Procurement Request</h3>

            <form action="{{ route('staff.requests.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block">Requestor</label>
                    <input type="text" 
       value="{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}"  
       class="border rounded p-2 w-full bg-gray-100" 
       readonly>

                </div>

                <div class="mb-4">
                    <label class="block">Office</label>
                    <input type="text" name="office" value="{{ auth()->user()->office->name }}" class="border rounded p-2 w-full bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block">Date Requested</label>
                    <input type="date" name="date_requested" class="border rounded p-2 w-full" required>
                </div>

                <h3 class="text-lg font-semibold mb-2">Selected Items</h3>
                <div id="selected-items-container"></div>

                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-4">Submit Request</button>
            </form>
        </div>

        <!-- Right Side: Items Usually Purchased -->
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">Items Usually Purchased</h3>
            <input type="text" id="searchItem" placeholder="Search items..." class="border rounded p-2 w-full mb-2" onkeyup="filterItems()">

            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2">Select</th>
                        <th class="border border-gray-300 p-2">Item Name</th>
                        <th class="border border-gray-300 p-2">Unit Price</th>
                    </tr>
                </thead>
                <tbody id="item-table-body">
                    @if(isset($existingItems) && count($existingItems) > 0)
                        @foreach($existingItems as $item)
                            @if($item->office_id == auth()->user()->office_id)
                                <tr>
                                    <td class="border border-gray-300 p-2 text-center">
                                        <input type="checkbox" value="{{ $item->item_name }}" 
                                               data-id="{{ $item->id }}" 
                                               data-price="{{ $item->unit_price }}" 
                                               onclick="addItemToRequest(this)">
                                    </td>
                                    <td class="border border-gray-300 p-2">{{ $item->item_name }}</td>
                                    <td class="border border-gray-300 p-2">{{ number_format($item->unit_price, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center text-gray-500 p-2">No items found for your office.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Button to Add New Item -->
            <button type="button" onclick="showAddItemModal()" class="bg-blue-500 text-white px-4 py-2 rounded mt-4 w-full">
                + Add New Item
            </button>
        </div>
    </div>
</div>

<!-- Add New Item Modal (Hidden by default) -->
<div id="addItemModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-1/3">
        <h3 class="text-lg font-semibold mb-4">Add New Item</h3>

        <label class="block mb-2">Item Name</label>
        <input type="text" id="newItemName" class="border rounded p-2 w-full mb-2">

        <label class="block mb-2">Unit Price</label>
        <input type="number" step="0.01" id="newItemPrice" class="border rounded p-2 w-full mb-4">

        <button onclick="addNewItem()" class="bg-green-500 text-white px-4 py-2 rounded">Add Item</button>
        <button onclick="hideAddItemModal()" class="bg-red-500 text-white px-4 py-2 rounded ml-2">Cancel</button>
    </div>
</div>

<script>
    function addItemToRequest(checkbox) {
        if (checkbox.checked) {
            const itemId = checkbox.getAttribute('data-id');
            const itemName = checkbox.value;
            const unitPrice = checkbox.getAttribute('data-price') || 0;

            const container = document.getElementById('selected-items-container');
            const itemHtml = `
                <div class="mb-4 border p-4 rounded" id="selected-item-${itemId}">
                    <input type="hidden" name="items[${itemId}][item_name]" value="${itemName}">
                    <label>Item: ${itemName}</label>
                    <input type="number" name="items[${itemId}][quantity]" placeholder="Quantity" class="border rounded p-2 w-full" required min="1">
                    <input type="number" name="items[${itemId}][unit_price]" value="${unitPrice}" class="border rounded p-2 w-full mt-2" readonly>
                    <button type="button" onclick="removeSelectedItem(${itemId})" class="bg-red-500 text-white px-2 py-1 mt-2 rounded">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        } else {
            removeSelectedItem(checkbox.getAttribute('data-id'));
        }
    }

    function removeSelectedItem(itemId) {
        document.getElementById(`selected-item-${itemId}`)?.remove();
        document.querySelector(`input[data-id="${itemId}"]`).checked = false;
    }

    function filterItems() {
        let input = document.getElementById("searchItem").value.toLowerCase();
        let rows = document.querySelectorAll("#item-table-body tr");

        rows.forEach(row => {
            let itemName = row.cells[1].textContent.toLowerCase();
            row.style.display = itemName.includes(input) ? "" : "none";
        });
    }

    function showAddItemModal() {
        document.getElementById("addItemModal").classList.remove("hidden");
    }

    function hideAddItemModal() {
        document.getElementById("addItemModal").classList.add("hidden");
    }

    function addNewItem() {
        let itemName = document.getElementById("newItemName").value.trim();
        let itemPrice = document.getElementById("newItemPrice").value.trim();

        if (itemName === "" || itemPrice === "") {
            alert("Please fill in all fields.");
            return;
        }

        const container = document.getElementById("item-table-body");
        const newItemHtml = `
            <tr>
                <td class="border border-gray-300 p-2 text-center">
                    <input type="checkbox" value="${itemName}" data-price="${itemPrice}" onclick="addItemToRequest(this)">
                </td>
                <td class="border border-gray-300 p-2">${itemName}</td>
                <td class="border border-gray-300 p-2">${parseFloat(itemPrice).toFixed(2)}</td>
            </tr>
        `;

        container.insertAdjacentHTML("beforeend", newItemHtml);
        hideAddItemModal();
    }
</script>
@endsection
