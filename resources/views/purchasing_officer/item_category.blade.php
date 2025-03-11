@extends('layouts.purchasingapp')

@section('content')
<div class="bg-white p-6 shadow rounded">
    <h2 class="text-lg font-semibold mb-4">Manage Item Categories</h2>

    <form method="POST" action="{{ route('item-categories.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Category Name" class="border p-2 w-full mb-2" required>
        <textarea name="description" placeholder="Description (optional)" class="border p-2 w-full mb-2"></textarea>
        <button type="submit" class="bg-blue-500 text-white p-2 w-full">Add Category</button>
    </form>

    <h3 class="text-lg font-semibold mt-6">Existing Categories</h3>
    <table class="w-full border-collapse border border-gray-300 mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">ID</th>
                <th class="border p-2">Category Name</th>
                <th class="border p-2">Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td class="border p-2">{{ $category->id }}</td>
                <td class="border p-2">{{ $category->name }}</td>
                <td class="border p-2">{{ $category->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
