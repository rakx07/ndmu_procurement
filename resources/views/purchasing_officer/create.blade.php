@extends('layouts.purchasingapp')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Left Side: Add Item Form -->
        <div class="col-md-4">
            <div class="card shadow-sm p-4">
                <h2 class="text-2xl font-semibold mb-4">Create New Item</h2>
                <form action="{{ route('purchasing_officer.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="item_name" class="form-control" placeholder="Enter item name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" value="1" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" placeholder="Enter supplier name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Category</label>
                        <select name="item_category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="unit_price" class="form-control" step="0.01" placeholder="Enter unit price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Excel File (Optional)</label>
                        <input type="file" name="excel_file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Item</button>
                </form>
            </div>
        </div>
        
        <!-- Right Side: Procurement Items Table -->
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Procurement Items</h4>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Supplier</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                                @foreach($procurementItems as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->supplier_name ?? 'No Supplier' }}</td>
                                    <td>{{ $item->category ? $item->category->name : 'No Category' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total_price, 2) }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($item->status) }}</span></td>
                                    <td>
                                        <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('purchasing_officer.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                Delete
                                            </button>
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
