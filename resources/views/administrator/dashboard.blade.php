@extends('layouts.administratorapp')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Procurement Requests Pending Administrator Approval</h1>

    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Request ID</th>
                <th class="px-4 py-2 border">Requested By</th>
                <th class="px-4 py-2 border">Department</th>
                <th class="px-4 py-2 border">Item Description</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pendingRequests as $key => $request)
                <tr class="border">
                    <td class="px-4 py-2 border">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $request->id }}</td>
                    <td class="px-4 py-2 border">{{ $request->user->name }}</td>
                    <td class="px-4 py-2 border">{{ $request->department }}</td>
                    <td class="px-4 py-2 border">{{ $request->item_description }}</td>
                    <td class="px-4 py-2 border text-yellow-500 font-bold">Pending</td>
                    <td class="px-4 py-2 border flex space-x-2">
                        <form action="{{ route('administrator.approve', $request->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded hover:bg-green-700">
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('administrator.reject', $request->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-700">
                                Reject
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No pending requests.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
