@extends('layouts.appitadmin')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h2 class="text-2xl font-bold text-[var(--primary-dark-green)] mb-4">Create New User</h2>

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('it_admin.store') }}" class="bg-white p-6 shadow-lg rounded-lg">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Employee ID -->
            <div>
                <label for="employee_id" class="block text-sm font-semibold text-gray-700">Employee ID</label>
                <input type="text" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="employee_id" name="employee_id" required>
            </div>

            <!-- Last Name -->
            <div>
                <label for="lastname" class="block text-sm font-semibold text-gray-700">Last Name</label>
                <input type="text" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="lastname" name="lastname" required>
            </div>

            <!-- First Name -->
            <div>
                <label for="firstname" class="block text-sm font-semibold text-gray-700">First Name</label>
                <input type="text" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="firstname" name="firstname" required>
            </div>

            <!-- Middle Name -->
            <div>
                <label for="middlename" class="block text-sm font-semibold text-gray-700">Middle Name (Optional)</label>
                <input type="text" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="middlename" name="middlename">
            </div>

            <!-- Email -->
            <div class="col-span-2">
                <label for="email" class="block text-sm font-semibold text-gray-700">Email Address</label>
                <input type="email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="email" name="email" required>
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700">Role</label>
                <select class="w-full p-2 border rounded-lg bg-white focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="role" name="role" required>
                    <option value="0">Staff</option>
                    <option value="1">Purchasing Officer</option>
                    <option value="2">Supervisor</option>
                    <option value="3">Administrator</option>
                    <option value="4">Comptroller</option>
                    <option value="5">IT Admin</option>
                </select>
            </div>

            <!-- Designation -->
            <div>
                <label for="designation" class="block text-sm font-semibold text-gray-700">Designation</label>
                <input type="text" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[var(--secondary-light-green)]" id="designation" name="designation" required>
            </div>
        </div>

        <button type="submit" class="mt-6 w-full bg-[var(--primary-dark-green)] text-white py-2 px-4 rounded-lg hover:bg-[var(--hover-green)] transition">
            Create User
        </button>
    </form>
</div>
@endsection
