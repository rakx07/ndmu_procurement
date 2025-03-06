@extends('layouts.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Your Procurement Requests</h2>

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Requestor</th>
                <th class="border px-4 py-2">Office</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Total Items</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td class="border px-4 py-2">{{ $request->id }}</td>
                    <td class="border px-4 py-2">{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}</td>
                    <td class="border px-4 py-2">{{ $request->office }}</td>
                    <td class="border px-4 py-2">{{ ucfirst($request->status) }}</td>
                    <td class="border px-4 py-2">
                        {{ $request->items->count() }} items
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('staff.requests.show', $request->id) }}" class="text-blue-500">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 p-4">No procurement requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
