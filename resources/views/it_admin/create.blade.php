@extends('layouts.appitadmin')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <h2 class="text-2xl font-bold text-green-700 text-center mb-6 uppercase">Create New User</h2>

                @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center p-2 rounded-lg mb-3" role="alert">
                <strong>User Created Successfully!</strong> <br>
                @if(session('email') && session('temp_password'))
                    Email: <strong>{{ session('email') }}</strong> <br>
                    Temporary Password: <strong>{{ session('temp_password') }}</strong>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif


    <form method="POST" action="{{ route('it_admin.store') }}" id="userForm" class="bg-white p-6 rounded-lg shadow-md">
        @csrf

        <div class="row g-3">
            <!-- Employee ID & Email -->
            <div class="col-md-6">
                <label for="employee_id" class="form-label fw-bold">Employee ID</label>
                <input type="text" id="employee_id" name="employee_id" required class="form-control required-field">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label fw-bold">Email Address</label>
                <input type="email" id="email" name="email" required class="form-control required-field">
            </div>

            <!-- Last Name, First Name, Middle Name -->
            <div class="col-md-6">
                <label for="lastname" class="form-label fw-bold">Last Name</label>
                <input type="text" id="lastname" name="lastname" required class="form-control required-field">
            </div>
            <div class="col-md-6">
                <label for="firstname" class="form-label fw-bold">First Name</label>
                <input type="text" id="firstname" name="firstname" required class="form-control required-field">
            </div>
            <div class="col-md-6">
                <label for="middlename" class="form-label fw-bold">Middle Name (Optional)</label>
                <input type="text" id="middlename" name="middlename" class="form-control">
            </div>

            <!-- Role -->
            <div class="col-md-6">
                <label for="role" class="form-label fw-bold">Role</label>
                <select id="role" name="role" required class="form-select required-field">
                    <option value="">Select Role</option>
                    <option value="0">Staff</option>
                    <option value="1">Purchasing Officer</option>
                    <option value="2">Supervisor</option>
                    <option value="3">Administrator</option>
                    <option value="4">Comptroller</option>
                    <option value="5">IT Admin</option>
                    <option value="6">Book Room</option>
                    <option value="5">Physical Plant Inventory Officer</option>
                </select>
            </div>

            <!-- Supervisor Selection (For Staff & Supervisors) -->
            <div class="col-md-6 d-none" id="supervisorField">
                <label for="supervisor_id" class="form-label fw-bold">Assign Supervisor</label>
                <select id="supervisor_id" name="supervisor_id" class="form-select">
                    <option value="">Select Supervisor</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}">{{ $supervisor->firstname }} {{ $supervisor->lastname }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Administrator Selection (For Staff & Supervisors) -->
            <div class="col-md-6 d-none" id="adminField">
                <label for="administrator_id" class="form-label fw-bold">Assign Administrator</label>
                <select id="administrator_id" name="administrator_id" class="form-select">
                    <option value="">Select Administrator</option>
                    @foreach($administrators as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->firstname }} {{ $admin->lastname }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Office Dropdown -->
            <div class="col-md-6">
                <label for="office_id" class="form-label fw-bold">Office</label>
                <select id="office_id" name="office_id" required class="form-select required-field">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Buttons Section -->
        <div class="mt-4 text-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" id="submitBtn" class="btn btn-primary px-4" disabled>Save</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const supervisorField = document.getElementById('supervisorField');
        const adminField = document.getElementById('adminField');
        const submitBtn = document.getElementById('submitBtn');
        const requiredFields = document.querySelectorAll('.required-field');

        function checkFormCompletion() {
            let isComplete = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isComplete = false;
                }
            });

            if (isComplete) {
                submitBtn.classList.remove('btn-secondary', 'disabled');
                submitBtn.classList.add('btn-primary');
                submitBtn.disabled = false;
            } else {
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary', 'disabled');
                submitBtn.disabled = true;
            }
        }

        roleSelect.addEventListener('change', function () {
            let selectedRole = roleSelect.value;

            if (selectedRole == '0') { // Staff: Show both Supervisor and Administrator dropdowns
                supervisorField.classList.remove('d-none');
                adminField.classList.remove('d-none');
            } else if (selectedRole == '2') { // Supervisor: Only show Administrator dropdown
                adminField.classList.remove('d-none');
                supervisorField.classList.add('d-none');
            } else { // Hide both for other roles
                supervisorField.classList.add('d-none');
                adminField.classList.add('d-none');
            }
        });

        requiredFields.forEach(field => {
            field.addEventListener('input', checkFormCompletion);
            field.addEventListener('change', checkFormCompletion);
        });

        checkFormCompletion();
    });
</script>
@endsection
