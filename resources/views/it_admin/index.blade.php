@extends('layouts.appitadmin')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">User Management (IT Admin Dashboard)</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr class="text-center">
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
                <tr class="align-middle text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->employee_id }}</td>
                    <td>{{ $user->lastname }}</td>
                    <td>{{ $user->firstname }}</td>
                    <td>{{ $user->middlename ?? 'N/A' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roleText() }}</td>
                    <td>{{ $user->office->name ?? 'N/A' }}</td>
                    <td>
                        <span id="status-{{ $user->id }}" 
                              class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}" 
                              style="cursor: pointer; font-size: 14px;" 
                              onclick="toggleUserStatus({{ $user->id }})">
                            {{ $user->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
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
</div>

<!-- ✅ Full-Screen Modal for Editing Users -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="user_id" name="user_id">

                    <div class="row g-3">
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

                    <div class="row g-3 mt-2">
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
                            <select class="form-control" id="role" name="role" onchange="toggleRoleFields()">
                                <option value="0">Staff</option>
                                <option value="1">Purchasing Officer</option>
                                <option value="2">Supervisor</option>
                                <option value="3">Administrator</option>
                                <option value="4">Comptroller</option>
                                <option value="5">IT Admin</option>
                                <option value="6">Book ROom</option>
                                <option value="5">Physical Plant Inventory</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6" id="supervisorField">
                            <label for="supervisor_id" class="form-label">Assign Supervisor</label>
                            <select class="form-control" id="supervisor_id" name="supervisor_id">
                                <option value="">Select Supervisor</option>
                                @foreach ($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->firstname }} {{ $supervisor->lastname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="adminField">
                            <label for="administrator_id" class="form-label">Assign Administrator</label>
                            <select class="form-control" id="administrator_id" name="administrator_id">
                                <option value="">Select Administrator</option>
                                @foreach ($administrators as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->firstname }} {{ $admin->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ✅ JavaScript for Modal Behavior -->
<script>
    function populateModal(user) {
        console.log(user); // ✅ Debugging

        let modalElement = document.getElementById('editUserModal');
        let editUserModal = new bootstrap.Modal(modalElement);

        let form = document.getElementById("editUserForm");
        form.setAttribute("action", `/it_admin/update/${user.id}`);

        document.getElementById("user_id").value = user.id;
        document.getElementById("employee_id").value = user.employee_id;
        document.getElementById("lastname").value = user.lastname;
        document.getElementById("firstname").value = user.firstname;
        document.getElementById("middlename").value = user.middlename ?? ''; 
        document.getElementById("email").value = user.email;
        document.getElementById("role").value = user.role;

        document.getElementById("supervisor_id").value = user.supervisor_id ?? '';
        document.getElementById("administrator_id").value = user.administrator_id ?? '';

        toggleRoleFields();
        editUserModal.show();
    }

    function toggleRoleFields() {
        let role = document.getElementById("role").value;
        document.getElementById('supervisorField').classList.toggle('d-none', role !== '0');
        document.getElementById('adminField').classList.toggle('d-none', role !== '0' && role !== '2');
    }
    document.addEventListener('DOMContentLoaded', function () {
    let editUserModal = document.getElementById('editUserModal');

    editUserModal.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
    });
});
</script>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
