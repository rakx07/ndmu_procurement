@extends('layouts.staffapp')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Ensure CSRF Token is available --}}

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
                    <input type="text" value="{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}" class="border rounded p-2 w-full bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block">Office</label>
                    <input type="text" name="office" value="{{ auth()->user()->office->name }}" class="border rounded p-2 w-full bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block">Date Requested</label>
                    <input type="date" name="date_requested" class="border rounded p-2 w-full" required 
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>

                <h3 class="text-lg font-semibold mb-2">Selected Items</h3>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 p-2">Item Name</th>
                            <th class="border border-gray-300 p-2">Unit Price</th>
                            <th class="border border-gray-300 p-2">Quantity</th>
                            <th class="border border-gray-300 p-2">Action</th>
                        </tr>
                    </thead>
                    <tbody id="selected-items-container"></tbody>
                </table>

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
                    @foreach($existingItems as $item)
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">
                                <input type="checkbox" value="{{ $item->item_name }}" 
                                       data-item-name="{{ $item->item_name }}" 
                                       data-price="{{ $item->unit_price }}" 
                                       onclick="addItemToRequest(this)">
                            </td>
                            <td class="border border-gray-300 p-2">{{ $item->item_name }}</td>
                            <td class="border border-gray-300 p-2">{{ number_format($item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $existingItems->links() }}
            </div>

            <!-- Button to Add New Item -->
            <button type="button" onclick="showAddItemModal()" class="bg-blue-500 text-white px-4 py-2 rounded mt-4 w-full">
                + Add New Item
            </button>
        </div>
    </div>
</div>

<!-- Add New Item Modal -->
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
    let selectedItems = {}; // Store selected items to retain them

    function addItemToRequest(checkbox) {
        const itemName = checkbox.getAttribute('data-item-name');
        const unitPrice = checkbox.getAttribute('data-price') || 0;

        if (checkbox.checked) {
            selectedItems[itemName] = { unit_price: unitPrice, quantity: 1 };
            updateSelectedItemsTable();
        } else {
            delete selectedItems[itemName];
            updateSelectedItemsTable();
        }
    }

    function updateSelectedItemsTable() {
        const container = document.getElementById('selected-items-container');
        container.innerHTML = ''; // Clear table before updating

        Object.keys(selectedItems).forEach((itemName) => {
            const item = selectedItems[itemName];
            const itemHtml = `
                <tr id="selected-item-${itemName.replace(/\s+/g, '-')}">
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="items[${itemName}][item_name]" value="${itemName}" class="border rounded p-2 w-full" readonly>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="number" name="items[${itemName}][unit_price]" value="${item.unit_price}" class="border rounded p-2 w-full" readonly>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="number" name="items[${itemName}][quantity]" class="border rounded p-2 w-full" required min="1" value="${item.quantity}"
                            onchange="selectedItems['${itemName}'].quantity = this.value">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <button type="button" onclick="removeSelectedItem('${itemName.replace(/\s+/g, '-')}')" class="bg-red-500 text-white px-2 py-1 rounded">Remove</button>
                    </td>
                </tr>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    function removeSelectedItem(itemName) {
        const actualName = itemName.replace(/-/g, ' '); // Restore spaces
        delete selectedItems[actualName];
        updateSelectedItemsTable();
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

    fetch("{{ route('staff.requests.addItem') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ item_name: itemName, unit_price: parseFloat(itemPrice) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addItemToTable(itemName, itemPrice); // Append new item without reloading
            hideAddItemModal();
            
            // ✅ Clear the modal input fields after adding the item
            document.getElementById("newItemName").value = "";
            document.getElementById("newItemPrice").value = "";
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Fetch error:", error));
}

function hideAddItemModal() {
    document.getElementById("addItemModal").classList.add("hidden");
}


    function addItemToTable(itemName, unitPrice) {
        const tableBody = document.getElementById("item-table-body");

        const newRow = `
            <tr>
                <td class="border border-gray-300 p-2 text-center">
                    <input type="checkbox" value="${itemName}" 
                           data-item-name="${itemName}" 
                           data-price="${unitPrice}" 
                           onclick="addItemToRequest(this)">
                </td>
                <td class="border border-gray-300 p-2">${itemName}</td>
                <td class="border border-gray-300 p-2">${parseFloat(unitPrice).toFixed(2)}</td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', newRow);
    }
</script>

@endsection
