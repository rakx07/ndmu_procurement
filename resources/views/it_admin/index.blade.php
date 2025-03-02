@extends('layouts.appitadmin')

@section('content')
<div class="container">
    <h2 class="mb-4">User Management (IT Admin Dashboard)</h2>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middlename</th>
                <th>Email</th>
                <th>Role</th>
                <th>Office</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->employee_id }}</td>
                <td>{{ $user->lastname }}</td>
                <td>{{ $user->firstname }}</td>
                <td>{{ $user->middlename ?? 'N/A' }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->roleText() }}</td>
                <td>{{ $user->office->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}">
                        {{ $user->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <!-- ✅ Fix: JSON Encode to ensure valid JavaScript object -->
                    <button class="btn btn-warning btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editUserModal"
                        onclick="populateModal({{ json_encode($user) }})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ✅ Full-Screen Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- ✅ Full-width modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="user_id" name="user_id">

                    <div class="row">
                        <div class="col-md-4">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                        </div>
                        <div class="col-md-4">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="col-md-4">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="middlename" class="form-label">Middlename</label>
                            <input type="text" class="form-control" id="middlename" name="middlename">
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-4">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role">
                                <option value="0">Staff</option>
                                <option value="1">Purchasing Officer</option>
                                <option value="2">Supervisor</option>
                                <option value="3">Administrator</option>
                                <option value="4">Comptroller</option>
                                <option value="5">IT Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="office_id" class="form-label">Office</label>
                            <select class="form-control" id="office_id" name="office_id">
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ✅ JavaScript for Modal -->
<script>
    function populateModal(user) {
        console.log("User Data:", user); // ✅ Debugging

        // Ensure the form action is set correctly
        document.getElementById("editUserForm").action = `/users/${user.id}/update`;

        // Populate fields
        document.getElementById("user_id").value = user.id;
        document.getElementById("employee_id").value = user.employee_id;
        document.getElementById("lastname").value = user.lastname;
        document.getElementById("firstname").value = user.firstname;
        document.getElementById("middlename").value = user.middlename ?? ''; // ✅ Fix: Include middlename
        document.getElementById("email").value = user.email;
        document.getElementById("role").value = user.role; // ✅ Fix role selection
        document.getElementById("status").value = user.status ? 1 : 0;

        // ✅ Fix: Select the correct office in the dropdown
        let officeDropdown = document.getElementById("office_id");
        for (let i = 0; i < officeDropdown.options.length; i++) {
            if (officeDropdown.options[i].value == user.office_id) {
                officeDropdown.options[i].selected = true;
                break;
            }
        }

        // ✅ Ensure modal opens properly
        let editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();

        // ✅ Fix: Ensure modal closes properly by removing backdrop manually
        document.getElementById('editUserModal').addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
    }
</script>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
