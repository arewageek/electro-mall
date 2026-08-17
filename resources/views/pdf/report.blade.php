<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activity Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h2 { margin-bottom: 5px; }
        .meta { color: #52525b; font-size: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>{{ config('app.name') }} - Activity Report</h2>
    <div class="meta">
        Generated on: {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Location</th>
                <th>Qty</th>
                <th>User</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $log->type)) }}</td>
                    <td>{{ $log->product->category->name ?? 'N/A' }}</td>
                    <td>{{ $log->product->name }}</td>
                    <td>{{ $log->product->sku }}</td>
                    <td>{{ $log->location ? implode('/', array_filter([$log->location->zone, $log->location->aisle, $log->location->rack])) : 'Unknown' }}</td>
                    <td class="text-center">{{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ $log->reference }}</td>
                </tr>
            @endforeach
            @if($transactions->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">No activities found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
