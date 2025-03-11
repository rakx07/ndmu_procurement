@extends('layouts.purchasingapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Left Side: Add Category Form -->
        <div class="col-md-4">
            <div class="card shadow-sm p-4">
                <h2 class="text-lg font-semibold mb-4">Add New Item Category</h2>
                <form method="POST" action="{{ route('item-categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" placeholder="Enter description (optional)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Category</button>
                </form>
            </div>
        </div>

        <!-- Right Side: Categories Table -->
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Existing Item Categories</h4>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('item-categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
