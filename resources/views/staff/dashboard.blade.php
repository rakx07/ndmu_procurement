<!-- resources/views/staff/dashboard.blade.php -->
@extends('layouts.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">My Requests</h2>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Office</th>
                <th class="border px-4 py-2">Date Requested</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Remarks</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td class="border px-4 py-2">{{ $request->id }}</td>
                    <td class="border px-4 py-2">{{ $request->office }}</td>
                    <td class="border px-4 py-2">{{ $request->date_requested }}</td>
                    <td class="border px-4 py-2">{{ $request->status }}</td>
                    <td class="border px-4 py-2">{{ $request->remarks }}</td>
                    <td class="border px-4 py-2">
                        @if($request->status == 'pending' || $request->editable_by_supervisor)
                            <a href="{{ route('staff.requests.edit', $request->id) }}" class="text-blue-500">Edit</a>
                        @else
                            <span class="text-gray-400">Locked</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection