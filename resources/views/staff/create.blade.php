@extends('layouts.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Create Request</h2>

    <form action="{{ route('staff.requests.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block">Office</label>
            <input type="text" name="office" class="border rounded p-2 w-full" required>
        </div>

        <div class="mb-4">
            <label class="block">Date Requested</label>
            <input type="date" name="date_requested" class="border rounded p-2 w-full" required>
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Submit Request</button>
    </form>
</div>
@endsection
