@extends('layouts.staffapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Left Side: Procurement Request Form -->
        <div class="col-lg-4">
            <div class="card shadow-sm p-4">
                <h2 class="text-2xl font-semibold mb-4">Create Procurement Request</h2>
                <form action="{{ route('staff.requests.store') }}" method="POST">
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

                    <!-- ✅ Needs Administrator Approval Checkbox -->
                    <div class="mb-3">
                        <input type="checkbox" id="needs_admin_approval" name="needs_admin_approval" value="1" onchange="updateApprovalFlow()">
                        <label for="needs_admin_approval" class="form-label fw-bold ms-2">Needs Administrator Approval?</label>
                    </div>

                    <!-- Hidden input to track approval levels -->
                    <input type="hidden" id="approval_flow" name="approval_flow" value="supervisor_comptroller_purchasing">

                    <h4 class="text-lg font-semibold mb-3">Selected Items</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="selected-items-container"></tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Submit Request</button>
                </form>
            </div>
        </div>

        <!-- Right Side: Available Procurement Items -->
        <div class="col-lg-8">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Available Items</h4>
                
                <div class="mb-3">
                    <input type="text" id="searchItem" class="form-control" placeholder="Search items..." onkeyup="filterItems()">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Item Name</th>
                                <th>Unit Price</th>
                                <th>Supplier</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="item-table-body">
                            @if($existingItems->count() > 0)
                                @foreach($existingItems as $item)
                                    <tr id="available-item-{{ $item->id }}">
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->supplier_name }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" 
                                                    id="add-btn-{{ $item->id }}" 
                                                    onclick="addItemToRequest({{ $item->id }}, '{{ $item->item_name }}', {{ $item->unit_price }})">
                                                Add
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-danger">No available items found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $existingItems->links() }}
                </div>
            </div>

            <!-- ✅ Other Items Section (Manual Entry) -->
            <div class="card shadow-sm p-4 mt-4">
                <h4 class="mb-3">Other Items</h4>
                
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" id="manualItemName" class="form-control" placeholder="Item Name">
                    </div>
                    <div class="col-md-3">
                        <input type="number" id="manualItemPrice" class="form-control" placeholder="Unit Price" min="0">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" onclick="addManualItem()">Add Item</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedItems = {}; // Store selected items

    function addItemToRequest(itemId, itemName, unitPrice) {
        if (!selectedItems[itemId]) {
            selectedItems[itemId] = { item_name: itemName, unit_price: unitPrice, quantity: 1 };
            updateSelectedItemsTable();

            // Disable Add Button
            let addButton = document.getElementById(`add-btn-${itemId}`);
            addButton.classList.remove("btn-success");
            addButton.classList.add("btn-secondary");
            addButton.disabled = true;
        }
    }

    function addManualItem() {
        let itemName = document.getElementById('manualItemName').value.trim();
        let unitPrice = parseFloat(document.getElementById('manualItemPrice').value);

        if (itemName === "" || isNaN(unitPrice) || unitPrice < 0) {
            alert("Please enter a valid item name and unit price.");
            return;
        }

        let itemId = 'manual-' + itemName.replace(/\s+/g, '-').toLowerCase();

        if (selectedItems[itemId]) {
            alert("This item is already added.");
            return;
        }

        selectedItems[itemId] = { item_name: itemName, unit_price: unitPrice, quantity: 1 };
        updateSelectedItemsTable();

        // Clear input fields
        document.getElementById('manualItemName').value = "";
        document.getElementById('manualItemPrice').value = "";
    }

    function updateSelectedItemsTable() {
        const container = document.getElementById('selected-items-container');
        container.innerHTML = ''; // Clear table before updating

        Object.keys(selectedItems).forEach((itemId) => {
            const item = selectedItems[itemId];
            const itemHtml = `
                <tr id="selected-item-${itemId}">
                    <td>${item.item_name}</td>
                    <td>${item.unit_price.toFixed(2)}</td>
                    <td><input type="number" class="form-control" required min="1" value="${item.quantity}" onchange="selectedItems['${itemId}'].quantity = this.value"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedItem('${itemId}')">Remove</button></td>
                </tr>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    function removeSelectedItem(itemId) {
        delete selectedItems[itemId];
        updateSelectedItemsTable();
    }
</script>

@endsection
