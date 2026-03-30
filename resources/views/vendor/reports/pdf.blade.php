<!DOCTYPE html>
<html>
<head>
    <title>Stylekart Report</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf-report.css') }}">
</head>
<body>
    <div class="header text-center">
        <h1>STYLEKART BUSINESS REPORT</h1>
        <p>{{ $stats['type_label'] }} | Period: {{ $stats['date_string'] }}</p>
    </div>

    <div class="summary">
        <p><strong>Vendor:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Total Transactions:</strong> {{ $stats['total_count'] }}</p>
        <p><strong>Total Value:</strong> RS {{ number_format($stats['total_value'], 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>Product</th>
                <th>Qty</th>
                @if($type !== 'delivered')
                    <th>Return Reason</th>
                @endif
                <th class="text-right">Amount (RS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $item)
            <tr>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>#{{ $item->order->order_number }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                @if($type !== 'delivered')
                    <td>{{ $item->return_reason ?? 'N/A' }}</td>
                @endif
                <td class="text-right">{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated via Stylekart Vendor Portal on {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
