@extends('layouts.staffapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Left Side: Dashboard Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm p-4">
                <h2 class="text-2xl font-semibold mb-4">Dashboard Overview</h2>

                <div class="mb-3">
                    <h4 class="text-lg font-semibold">Total Requests</h4>
                    <p class="text-xl font-bold">{{ $requests->count() }}</p>
                </div>

                <div class="mb-3">
                    <h4 class="text-lg font-semibold">Pending Requests</h4>
                    <p class="text-xl font-bold text-warning">{{ $requests->where('status', 'pending')->count() }}</p>
                </div>

                <div class="mb-3">
                    <h4 class="text-lg font-semibold">Approved Requests</h4>
                    <p class="text-xl font-bold text-success">{{ $requests->where('status', 'approved')->count() }}</p>
                </div>

                <div class="mb-3">
                    <h4 class="text-lg font-semibold">Rejected Requests</h4>
                    <p class="text-xl font-bold text-danger">{{ $requests->where('status', 'rejected')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Procurement Requests Table -->
        <div class="col-lg-8">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Your Procurement Requests</h4>

                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Requestor</th>
                                <th>Office</th>
                                <th>Status</th>
                                <th>Total Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}</td>
                                    <td>{{ $request->office }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->items->count() }} items</td>
                                    <td>
                                        <button onclick="viewItems({{ $request->id }})" class="btn btn-primary btn-sm">View</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No procurement requests found.</td>
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

<!-- ✅ View Modal -->
<div id="viewModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Procurement Request Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody id="modal-items-container"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewItems(requestId) {
    fetch(`/staff/request/${requestId}/items`)
        .then(response => response.json())
        .then(data => {
            let container = document.getElementById('modal-items-container');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No items found</td></tr>';
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.item_name}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>${parseFloat(item.total_price).toFixed(2)}</td>
                        </tr>
                    `;
                    container.insertAdjacentHTML('beforeend', row);
                });
            }

            new bootstrap.Modal(document.getElementById('viewModal')).show();
        })
        .catch(error => console.error('Error fetching items:', error));
}
</script>
@endsection
