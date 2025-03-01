@extends('layouts.appitadmin')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold text-green-700 text-center mb-6 uppercase">User Management (IT Admin Dashboard)</h2>
    
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->employee_id }}</td>
                <td>{{ $user->lastname }}</td>
                <td>{{ $user->firstname }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @switch($user->role)
                        @case(0) Staff @break
                        @case(1) Purchasing Officer @break
                        @case(2) Supervisor @break
                        @case(3) Administrator @break
                        @case(4) Comptroller @break
                        @case(5) IT Admin @break
                        @default Unknown
                    @endswitch
                </td>
                <td>
                    <span class="badge {{ $user->status == 1 ? 'bg-success' : 'bg-danger' }}">
                        {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('it_admin.create') }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
