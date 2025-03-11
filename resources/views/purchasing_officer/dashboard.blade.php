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
                    <td>{{ $request->requestor->name }}</td>
                    <td>{{ $request->office }}</td>
                    <td>{{ $request->date_requested }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($request->status) }}</span></td>
                    <td>{{ $request->approved_by }}</td>
                    <td>{{ $request->remarks }}</td>
                    <td>
                        <a href="#" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
