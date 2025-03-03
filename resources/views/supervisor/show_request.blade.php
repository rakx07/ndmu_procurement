@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Procurement Request Details</h2>
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
            <td><span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'success' }}">{{ $request->status }}</span></td>
        </tr>
        <tr>
            <th>Remarks</th>
            <td>{{ $request->remarks ?? 'No remarks' }}</td>
        </tr>
    </table>

    <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>
@endsection
