<!DOCTYPE html>
<html>
<head>
    <title>Procurement Request Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2, h4 {
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        .section-title {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Procurement Request Report</h2>
    <hr>

    <p><strong>Request ID:</strong> {{ $request->id }}</p>
    <p><strong>Requestor:</strong> {{ $request->requestor?->full_name ?? 'N/A' }}</p>
    <p><strong>Office:</strong> {{ $request->office ?? 'N/A' }}</p>
    <p><strong>Date Requested:</strong> {{ $request->date_requested }}</p>
    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $request->status)) }}</p>

    <div class="section-title">Requested Items:</div>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($request->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->description ?? 'N/A' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit ?? 'pc' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No items available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <p><strong>Comptroller:</strong> {{ $comptrollerName }}</p>
</body>
</html>
