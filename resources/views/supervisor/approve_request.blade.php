@extends('layouts.supervisorapp')

@section('content')
<div class="container">
    <h2>Approve Procurement Request</h2>
    <p>Are you sure you want to approve this request?</p>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $request->id }}</td>
        </tr>
        <tr>
            <th>Requestor</th>
            <td>{{ $request->user->firstname }} {{ $request->user->lastname }}</td>
        </tr>
        <tr>
            <th>Office</th>
            <td>{{ $request->office }}</td>
        </tr>
        <tr>
            <th>Date Requested</th>
            <td>{{ $request->date_requested }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="badge bg-warning">{{ $request->status }}</span></td>
        </tr>
    </table>

    <form action="{{ route('supervisor.approve', $request->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">Approve</button>
    </form>

    <form action="{{ route('supervisor.reject', $request->id) }}" method="POST" class="mt-2">
        @csrf
        <label for="remarks">Reason for Rejection (Optional)</label>
        <textarea name="remarks" class="form-control" rows="3"></textarea>
        <button type="submit" class="btn btn-danger mt-2">Reject</button>
    </form>

    <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary mt-3">Cancel</a>
</div>
@endsection
