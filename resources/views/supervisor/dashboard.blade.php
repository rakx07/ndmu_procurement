@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Supervisor Dashboard</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Requestor</th>
                <th>Office</th>
                <th>Date Requested</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->user->firstname }} {{ $request->user->lastname }}</td>
                    <td>{{ $request->office }}</td>
                    <td>{{ $request->date_requested }}</td>
                    <td><span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'success' }}">{{ $request->status }}</span></td>
                    <td>
                        <a href="{{ route('supervisor.show', $request->id) }}" class="btn btn-info btn-sm">View</a>
                        @if($request->status == 'pending')
                            <a href="{{ route('supervisor.approve_request', $request->id) }}" class="btn btn-success btn-sm">Approve</a>
                            <form action="{{ route('supervisor.reject', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
