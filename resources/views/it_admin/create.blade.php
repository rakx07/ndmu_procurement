@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create New User</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('it_admin.store') }}">
        @csrf

        <div class="form-group">
            <label for="employee_id">Employee ID</label>
            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
        </div>

        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>

        <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>

        <div class="form-group">
            <label for="middlename">Middle Name (Optional)</label>
            <input type="text" class="form-control" id="middlename" name="middlename">
        </div>

        <div class="form-group">
            <label for="email">Email Address (Username)</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="0">Staff</option>
                <option value="1">Purchasing Officer</option>
                <option value="2">Supervisor</option>
                <option value="3">Administrator</option>
                <option value="4">Comptroller</option>
                <option value="5">IT Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="designation">Designation</label>
            <input type="text" class="form-control" id="designation" name="designation" required>
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection
