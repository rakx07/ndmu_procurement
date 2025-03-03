@extends('staff.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Request</h2>

    <form action="{{ route('staff.requests.update', $request->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block">Office</label>
            <input type="text" name="office" class="border rounded p-2 w-full" value="{{ $request->office }}" required>
        </div>

        <div class="mb-4">
            <label class="block">Date Requested</label>
            <input type="date" name="date_requested" class="border rounded p-2 w-full" value="{{ $request->date_requested }}" required>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Request</button>
    </form>
</div>
@endsection
