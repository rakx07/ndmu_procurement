@extends('layouts.purchasingapp')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Purchasing Officer Dashboard</h1>

    <!-- Dashboard Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Total Requests</h5>
                <p class="fs-3">{{ $totalRequests }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Pending Requests</h5>
                <p class="fs-3">{{ $pendingRequests }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Approved Requests</h5>
                <p class="fs-3">{{ $approvedRequests }}</p>
            </div>
        </div>
    </div>

    <!-- Procurement Requests Table -->
    <div class="card shadow-sm mt-4 p-3">
        <h4 class="mb-3">Recent Procurement Requests</h4>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Requestor</th>
                    <th>Office</th>
                    <th>Date Requested</th>
                    <th>Status</th>
                    <th>Approved By</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($procurementRequests as $request)
                <tr>
                    <td>{{ $request->id }}</td>

                    <!-- ✅ Correctly display the Requestor (Staff who made the request) -->
                    <td>{{ optional($request->requestor)->name }}</td>

                    <td>{{ $request->office }}</td>
                    <td>{{ $request->date_requested }}</td>

                    <!-- ✅ Status Badge -->
                    <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span></td>

                    <!-- ✅ Display Approvers' Names -->
                    <td>
                        @php
                            $approverNames = collect();
                            foreach ($request->approvals as $approval) {
                                if ($approval->approver) {
                                    $approverNames->push($approval->approver->name);
                                }
                            }
                        @endphp
                        {{ $approverNames->isNotEmpty() ? $approverNames->implode(', ') : 'N/A' }}
                    </td>

                    <td>{{ $request->remarks }}</td>

                    <td>
                        <!-- ✅ View Items Button (Triggers Modal) -->
                        <button class="btn btn-sm btn-primary view-items-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#viewItemsModal" 
                            data-request-id="{{ $request->id }}">
                            View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ✅ Modal for Viewing Request Items -->
<div class="modal fade" id="viewItemsModal" tabindex="-1" aria-labelledby="viewItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewItemsModalLabel">Request Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody id="modal-items-body">
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ✅ JavaScript for Fetching Modal Data -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById('viewItemsModal');
        const modalTitle = document.getElementById('viewItemsModalLabel');
        const modalBody = document.getElementById('modal-items-body');

        document.querySelectorAll('.view-items-btn').forEach(button => {
            button.addEventListener('click', function () {
                const requestId = this.getAttribute('data-request-id');

                // Update modal title
                modalTitle.textContent = `Request Items - ID #${requestId}`;

                // Fetch items via AJAX
                fetch(`/procurement-requests/${requestId}/items`)
                    .then(response => response.json())
                    .then(data => {
                        modalBody.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                modalBody.innerHTML += `
                                    <tr>
                                        <td>${item.item_name}</td>
                                        <td>${item.description || 'N/A'}</td>
                                        <td>${item.quantity}</td>
                                        <td>${item.unit}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            modalBody.innerHTML = '<tr><td colspan="4" class="text-center">No items found.</td></tr>';
                        }
                    })
                    .catch(error => console.error('Error fetching items:', error));
            });
        });
    });
</script>

@endsection
