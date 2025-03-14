@extends('layouts.staffapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Left Side: Procurement Request Form (50%) -->
        <div class="col-lg-6">
            <div class="card shadow-sm p-4">
                <h2 class="text-2xl font-semibold mb-4">Create Procurement Request</h2>
                <form action="{{ route('staff.requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Requestor</label>
                        <input type="text" value="{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Office</label>
                        <input type="text" name="office" value="{{ auth()->user()->office->name }}" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date Requested</label>
                        <input type="date" name="date_requested" class="form-control" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <input type="checkbox" name="requires_admin_approval" id="requiresAdminApproval" class="form-check-input">
                        <label for="requiresAdminApproval" class="form-check-label">Requires Administrator Approval</label>
                    </div>

                    <!-- Selected Items -->
                    <h4 class="text-lg font-semibold mb-3">Selected Items</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Attachment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="selected-items-container"></tbody>
                        </table>
                    </div>

                    <!-- Other Items -->
                    <h4 class="text-lg font-semibold mt-4">Other Items (Not in the List)</h4>
                    <div class="mb-3">
                        <input type="text" id="manualItemName" class="form-control mb-2" placeholder="Enter Item Name">
                        <input type="number" id="manualItemPrice" class="form-control mb-2" placeholder="Enter Unit Price" min="0" step="0.01">
                        <button type="button" class="btn btn-success w-100" onclick="addManualItem()">Add Item</button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-3">Submit Request</button>
                </form>
            </div>
        </div>

        <!-- Right Side: Available Procurement Items (50%) -->
        <div class="col-lg-6">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Available Items</h4>
                
                <div class="mb-3">
                    <input type="text" id="searchItem" class="form-control" placeholder="Search items..." onkeyup="filterItems()">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Select</th>
                                <th>Item Name</th>
                                <th>Unit Price</th>
                            </tr>
                        </thead>
                        <tbody id="item-table-body">
                            @foreach($existingItems as $item)
                                <tr class="text-center">
                                    <td>
                                        <input type="checkbox" 
                                            value="{{ $item->id }}" 
                                            data-item-name="{{ $item->item_name }}" 
                                            data-price="{{ $item->unit_price }}" 
                                            onchange="toggleItemSelection(this)">
                                    </td>
                                    <td class="text-start ps-3">{{ $item->item_name }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $existingItems->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ FIXED JAVASCRIPT (MULTIPLE SELECTION WORKS NOW) ✅ -->
<script>
    let selectedItems = {}; // Store selected items

    // Function to handle item selection and deselection
    function toggleItemSelection(checkbox) {
        const itemId = checkbox.value;
        const itemName = checkbox.getAttribute('data-item-name');
        const unitPrice = parseFloat(checkbox.getAttribute('data-price'));

        if (checkbox.checked) {
            // Add item if not already selected
            if (!selectedItems[itemId]) {
                selectedItems[itemId] = { 
                    item_name: itemName, 
                    unit_price: unitPrice, 
                    quantity: 1 
                };
            }
        } else {
            // Remove item when unchecked
            delete selectedItems[itemId];
        }

        updateSelectedItemsTable(); // Refresh the selected items table
    }

    // Function to add manually entered items
    function addManualItem() {
        const itemName = document.getElementById("manualItemName").value;
        const unitPrice = parseFloat(document.getElementById("manualItemPrice").value);

        if (itemName && !isNaN(unitPrice) && unitPrice > 0) {
            let itemId = 'manual-' + Date.now(); // Unique ID for manual items
            selectedItems[itemId] = { 
                item_name: itemName, 
                unit_price: unitPrice, 
                quantity: 1 
            };

            updateSelectedItemsTable();

            // Clear input fields after adding
            document.getElementById("manualItemName").value = '';
            document.getElementById("manualItemPrice").value = '';
        } else {
            alert("Please enter valid item details.");
        }
    }

    // Function to update the Selected Items table dynamically
    function updateSelectedItemsTable() {
        const container = document.getElementById('selected-items-container');
        container.innerHTML = ''; // Clear table before updating

        Object.entries(selectedItems).forEach(([itemId, item]) => {
            const itemHtml = `
                <tr class="text-center" id="selected-item-${itemId}">
                    <td>${item.item_name}
                        <input type="hidden" name="items[${itemId}][item_name]" value="${item.item_name}">
                    </td>
                    <td>₱${item.unit_price.toFixed(2)}
                        <input type="hidden" name="items[${itemId}][unit_price]" value="${item.unit_price}">
                    </td>
                    <td>
                        <input type="number" name="items[${itemId}][quantity]" class="form-control text-center" 
                            required min="1" value="${item.quantity}" 
                            onchange="updateItemQuantity('${itemId}', this.value)">
                    </td>
                    <td>
                        <input type="file" name="items[${itemId}][attachment]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedItem('${itemId}')">
                            Remove
                        </button>
                    </td>
                </tr>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    // Function to update item quantity dynamically
    function updateItemQuantity(itemId, newQuantity) {
        selectedItems[itemId].quantity = parseInt(newQuantity);
    }

    // Function to remove an item from the selected items list
    function removeSelectedItem(itemId) {
        delete selectedItems[itemId];

        // Uncheck the item if it's from the Available Items table
        const checkbox = document.querySelector(`input[type="checkbox"][value="${itemId}"]`);
        if (checkbox) checkbox.checked = false;

        updateSelectedItemsTable();
    }

    // Function to filter available items by search input
    function filterItems() {
        let searchValue = document.getElementById("searchItem").value.toLowerCase();
        let tableRows = document.querySelectorAll("#item-table-body tr");

        tableRows.forEach(row => {
            let itemName = row.cells[1].textContent.toLowerCase();
            row.style.display = itemName.includes(searchValue) ? "" : "none";
        });
    }

    // Ensure selected items persist visually when paginating (optional improvement)
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#item-table-body input[type="checkbox"]').forEach(checkbox => {
            if (selectedItems[checkbox.value]) {
                checkbox.checked = true;
            }
        });
    });
</script>

@endsection
