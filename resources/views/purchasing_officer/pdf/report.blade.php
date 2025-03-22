<!DOCTYPE html>
<html>
<head>
    <title>Procurement Request Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 60px;
        }
        .header h2, .header h4 {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .section-title {
            margin-top: 20px;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .signatures {
            margin-top: 50px;
        }
        .signature-block {
            display: inline-block;
            width: 45%;
            vertical-align: top;
            text-align: center;
            margin-right: 5%;
        }
        .qr-code {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>

    <!-- QR Code -->
    <div class="qr-code">
    {{ QrCode::size(80)->format('svg')->generate('Request ID: '.$request->id) }}
</div>

    <!-- Header -->
    <div class="header">
        <img src="{{ public_path('images/university-logo.png') }}" alt="University Logo">
        <h2>Notre Dame of Marbel University</h2>
        <h4>Procurement Request Report</h4>
    </div>

    <hr>

    <!-- Request Info -->
    <p><strong>Request ID:</strong> {{ $request->id }}</p>
    <p><strong>Requestor:</strong> {{ $request->requestor?->full_name ?? 'N/A' }}</p>
    <p><strong>Office:</strong> {{ $request->office ?? 'N/A' }}</p>
    <p><strong>Date Requested:</strong> {{ $request->date_requested }}</p>
    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $request->status)) }}</p>

    <!-- Items Table -->
    <div class="section-title">Requested Items:</div>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($request->items as $item)
                @php
                    $unitPrice = floatval($item->unit_price ?? 0);
                    $quantity = floatval($item->quantity ?? 0);
                    $total = $unitPrice * $quantity;
                    $grandTotal += $total;
                @endphp
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->description ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit ?? 'pc' }}</td>
                    <td>₱{{ number_format($unitPrice, 2) }}</td>
                    <td>₱{{ number_format($total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No items available.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="5" class="text-end">Grand Total</td>
                <td>₱{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <p><strong>Comptroller:</strong> {{ $comptrollerName }}</p>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-block">
            <p>__________________________</p>
            <p>{{ $request->requestor?->full_name ?? 'Requestor' }}</p>
            <p><em>Requestor</em></p>
            <p>Date: ____________________</p>
        </div>

        <div class="signature-block">
            <p>__________________________</p>
            <p>{{ $comptrollerName ?? 'Comptroller' }}</p>
            <p><em>Comptroller</em></p>
            <p>Date: ____________________</p>
        </div>
    </div>

</body>
</html>
