@extends('layouts.staffapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Summary Boxes -->
        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h6 class="text-muted">Total Requests</h6>
                <h3 class="font-weight-bold">{{ $requests->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h6 class="text-muted">Pending Requests</h6>
                <h3 class="font-weight-bold text-warning">{{ $requests->where('status', 'pending')->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h6 class="text-muted">Approved Requests</h6>
                <h3 class="font-weight-bold text-success">{{ $requests->where('status', 'approved')->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h6 class="text-muted">Rejected Requests</h6>
                <h3 class="font-weight-bold text-danger">{{ $requests->where('status', 'rejected')->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Procurement Requests Table -->
    <div class="row mt-4">
        <div class="col">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Recent Procurement Requests</h5>

                <div class="table-responsive">
                    <table class="table table-bordered">
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
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->requestor->firstname . ' ' . $request->requestor->lastname }}</td>
                                    <td>{{ $request->office }}</td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $request->approver ? $request->approver->firstname . ' ' . $request->approver->lastname : 'N/A' }}
                                    </td>       
                                    <td>{{ $request->remarks ?? 'No remarks' }}</td>
                                    <td>
                                        <button onclick="viewItems({{ $request->id }})" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No procurement requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Items Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Requested Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody id="modal-items-container">
                        <tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function viewItems(requestId) {
    let container = document.getElementById('modal-items-container');
    container.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>'; // Show loading

    fetch(`/staff/requests/${requestId}/items`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = ''; // Clear the loading text

            if (data.length === 0) {
                container.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No items found</td></tr>';
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.item_name}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.unit_price || 0).toFixed(2)}</td>
                            <td>${(item.quantity * parseFloat(item.unit_price || 0)).toFixed(2)}</td>
                        </tr>
                    `;
                    container.insertAdjacentHTML('beforeend', row);
                });
            }
        })
        .catch(error => console.error('Error fetching items:', error));
}
</script>

@endsection
