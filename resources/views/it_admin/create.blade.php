@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create New User</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('it_admin.store') }}">
        @csrf

        <!-- Employee ID -->
        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee ID</label>
            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>

        <!-- First Name -->
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>

        <!-- Middle Name (Optional) -->
        <div class="mb-3">
            <label for="middlename" class="form-label">Middle Name (Optional)</label>
            <input type="text" class="form-control" id="middlename" name="middlename">
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Role Selection -->
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="0">Staff</option>
                <option value="1">Purchasing Officer</option>
                <option value="2">Supervisor</option>
                <option value="3">Administrator</option>
                <option value="4">Comptroller</option>
                <option value="5">IT Admin</option>
            </select>
        </div>

        <!-- Designation -->
        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" class="form-control" id="designation" name="designation" required>
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection
