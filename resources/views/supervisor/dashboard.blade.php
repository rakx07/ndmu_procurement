@extends('layouts.supervisorapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Supervisor Dashboard</h2>

    <!-- Check if there are any requests -->
    @if($requests->isEmpty())
        <p class="text-gray-600 text-lg">No procurement requests found.</p>
    @else
        <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
            <table class="w-full border-collapse border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Requestor</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Office</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Date Requested</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="px-4 py-2 border">{{ $request->id }}</td>
                            <td class="px-4 py-2 border">{{ $request->requestor->firstname }} {{ $request->requestor->lastname }}</td>
                            <td class="px-4 py-2 border">{{ $request->office }}</td>
                            <td class="px-4 py-2 border">{{ $request->date_requested }}</td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 text-white text-sm font-semibold rounded
                                    {{ $request->status == 'pending' ? 'bg-yellow-500' : 'bg-green-500' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border flex space-x-2">
                                <a href="{{ route('supervisor.show', $request->id) }}" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    View
                                </a>
                                @if($request->status == 'pending')
                                    <form action="{{ route('supervisor.approve', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('supervisor.reject', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                        @csrf
                                        <input type="text" name="remarks" placeholder="Reason for rejection" required class="border p-1 rounded">
                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
