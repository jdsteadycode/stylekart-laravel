<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #334155; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { text-align: left; background: #f8fafc; padding: 10px; border-bottom: 2px solid #e2e8f0; }
        .table td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .total { text-align: right; font-weight: bold; font-size: 18px; margin-top: 20px; color: #4f46e5; }
        .badge { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Stylekart</h1>
            <p>Thank you for your purchase, <strong>{{ $order->user->name }}</strong>!</p>
            <p style="color: #64748b;">Order #{{ $order->order_number }}</p>
        </div>

        <p>Your order has been received and is being processed by our vendors.</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product->name }}<br>
                        <span class="badge">Size: {{ $item->variant->size }} | Color: {{ $item->variant->color->name }}</span>
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total Amount: ₹{{ number_format($order->total_amount, 2) }}
        </div>

        <hr style="margin-top: 30px; border: 0; border-top: 1px solid #e2e8f0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">
            You chose <strong>{{ strtoupper($order->payment_mode) }}</strong> payment. <br>
            Stylekart Inc. • 2026
        </p>
    </div>
</body>
</html>
