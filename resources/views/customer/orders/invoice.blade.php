@extends('customer.layouts.app')

@section('title', 'Invoice - ' . $order->order_number)

@section('content')
<div class="bg-white min-h-screen py-12 px-6">
    <div class="max-w-3xl mx-auto border p-8 rounded-2xl shadow-lg">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-xl font-bold">{{ 'Stylekart' }}</h2>
            </div>
            <div class="text-right">
                <h2 class="text-sm font-bold">Invoice #: {{ $order->order_number }}</h2>
                <p class="text-xs text-gray-500">Date: {{ $order->created_at->format('d M, Y') }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-bold text-sm mb-2">Bill To:</h3>
            <p class="text-xs text-gray-700">
                {{ $order->user->name ?? 'Customer Name' }}<br>
                {{ $order->user->email ?? '' }}<br>
                {{ $order->address->address_line ?? 'Address' }}
            </p>
        </div>

        <table class="w-full border-collapse mb-6 text-xs">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">#</th>
                    <th class="border px-2 py-1">Product</th>
                    <th class="border px-2 py-1">Variant</th>
                    <th class="border px-2 py-1">Qty</th>
                    <th class="border px-2 py-1">Unit Price</th>
                    <th class="border px-2 py-1">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="border px-2 py-1">{{ $index + 1 }}</td>
                    <td class="border px-2 py-1">{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="border px-2 py-1">
                        Color: {{ $item->variant->color->name ?? '-' }}<br>
                        Size: {{ $item->variant->size ?? '-' }}
                    </td>
                    <td class="border px-2 py-1 text-center">{{ $item->quantity }}</td>
                    <td class="border px-2 py-1 text-right">₹ {{ number_format($item->price, 2) }}</td>
                    <td class="border px-2 py-1 text-right">₹ {{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mb-8">
            <p class="text-sm font-bold">Total: ₹ {{ number_format($order->total_amount, 2) }}</p>
        </div>

        <div class="text-center text-xs text-gray-500">
            Thank you for shopping with us!
        </div>

        <div class="mt-6 text-center">
            <button onclick="window.print()" class="px-4 py-2 bg-rose-500 text-white rounded-xl font-bold text-xs hover:bg-rose-600 transition-all">
                Print Invoice
            </button>
        </div>

    </div>
</div>
@endsection
