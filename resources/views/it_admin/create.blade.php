@extends('layouts.appitadmin')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <h2 class="text-2xl font-bold text-green-700 text-center mb-6 uppercase">Create New User</h2>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-center p-2 rounded-lg mb-3" role="alert">
        <strong>User Created Successfully!</strong> <br>
        @if(session('temp_password'))
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
                <input type="text" id="employee_id" name="employee_id" required 
                    class="form-control required-field">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label fw-bold">Email Address</label>
                <input type="email" id="email" name="email" required 
                    class="form-control required-field">
            </div>

            <!-- Last Name, First Name, Middle Name -->
            <div class="col-md-6">
                <label for="lastname" class="form-label fw-bold">Last Name</label>
                <input type="text" id="lastname" name="lastname" required 
                    class="form-control required-field">
            </div>
            <div class="col-md-6">
                <label for="firstname" class="form-label fw-bold">First Name</label>
                <input type="text" id="firstname" name="firstname" required 
                    class="form-control required-field">
            </div>
            <div class="col-md-6">
                <label for="middlename" class="form-label fw-bold">Middle Name (Optional)</label>
                <input type="text" id="middlename" name="middlename" 
                    class="form-control">
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
            <!-- Cancel Button -->
            <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">Cancel</a>

            <!-- Save Button -->
            <button type="submit" id="submitBtn" class="btn btn-primary px-4" disabled>Save</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('userForm');
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

        requiredFields.forEach(field => {
            field.addEventListener('input', checkFormCompletion);
            field.addEventListener('change', checkFormCompletion);
        });

        checkFormCompletion(); // Run on page load in case form is pre-filled
    });
</script>
@endsection
