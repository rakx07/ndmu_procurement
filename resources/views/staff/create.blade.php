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
                                <th>Select</th>
                                <th>Item Name</th>
                                <th>Unit Price</th>
                            </tr>
                        </thead>
                        <tbody id="item-table-body">
                            @foreach($existingItems as $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" 
                                               value="{{ $item->id }}" 
                                               data-item-name="{{ $item->item_name }}" 
                                               data-price="{{ $item->unit_price }}" 
                                               onclick="addItemToRequest(this)">
                                    </td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $existingItems->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedItems = {}; // Store selected items

    function addItemToRequest(checkbox) {
        const itemId = checkbox.value;
        const itemName = checkbox.getAttribute('data-item-name');
        const unitPrice = parseFloat(checkbox.getAttribute('data-price'));

        if (checkbox.checked) {
            selectedItems[itemId] = { item_name: itemName, unit_price: unitPrice, quantity: 1 };
            updateSelectedItemsTable();
        } else {
            delete selectedItems[itemId];
            updateSelectedItemsTable();
        }
    }

    function updateSelectedItemsTable() {
        const container = document.getElementById('selected-items-container');
        container.innerHTML = ''; // Clear table before updating

        Object.keys(selectedItems).forEach((itemId) => {
            const item = selectedItems[itemId];
            const itemHtml = `
                <tr id="selected-item-${itemId}">
                    <td>
                        <input type="hidden" name="items[${itemId}][item_name]" value="${item.item_name}">
                        ${item.item_name}
                    </td>
                    <td>
                        <input type="hidden" name="items[${itemId}][unit_price]" value="${item.unit_price}">
                        ${item.unit_price.toFixed(2)}
                    </td>
                    <td>
                        <input type="number" name="items[${itemId}][quantity]" class="form-control" required min="1" value="${item.quantity}"
                            onchange="selectedItems['${itemId}'].quantity = this.value">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedItem('${itemId}')">Remove</button>
                    </td>
                </tr>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });
    }

    function removeSelectedItem(itemId) {
        delete selectedItems[itemId];
        updateSelectedItemsTable();
    }

    function filterItems() {
        let searchValue = document.getElementById("searchItem").value.toLowerCase();
        let tableRows = document.querySelectorAll("#item-table-body tr");

        tableRows.forEach(row => {
            let itemName = row.cells[1].textContent.toLowerCase();
            row.style.display = itemName.includes(searchValue) ? "" : "none";
        });
    }

    function updateApprovalFlow() {
        let needsAdminApproval = document.getElementById('needs_admin_approval').checked;
        let approvalFlowInput = document.getElementById('approval_flow');

        if (needsAdminApproval) {
            approvalFlowInput.value = "supervisor_administrator_comptroller_purchasing";
        } else {
            approvalFlowInput.value = "supervisor_comptroller_purchasing";
        }
    }
</script>

@endsection
