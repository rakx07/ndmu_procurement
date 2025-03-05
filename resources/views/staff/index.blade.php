@extends('layouts.staffapp')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Procurement Requests</h2>

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
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td class="border px-4 py-2">{{ $request->id }}</td>
                    <td class="border px-4 py-2">{{ $request->requestor->name }}</td>
                    <td class="border px-4 py-2">{{ $request->office }}</td>
                    <td class="border px-4 py-2">{{ ucfirst($request->status) }}</td>
                    <td class="border px-4 py-2">
                        <a href="#" class="text-blue-500">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
