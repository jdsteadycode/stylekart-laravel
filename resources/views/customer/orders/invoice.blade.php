@extends('customer.layouts.app')

@section('title', __('order.invoice_title', ['number' => $order->order_number]))

@section('content')
<div class="bg-white min-h-screen py-12 px-6">
    <div class="max-w-3xl mx-auto border p-8 rounded-2xl shadow-lg">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-xl font-bold">{{ 'Stylekart' }}</h2>
            </div>
            <div class="text-right">
                <h2 class="text-sm font-bold">{{ __('order.invoice_hash') }} {{ $order->order_number }}</h2>
                <p class="text-xs text-gray-500">{{ __('order.date_label') }} {{ $order->created_at->format('d M, Y') }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-bold text-sm mb-2">{{ __('order.bill_to') }}</h3>
            <p class="text-xs text-gray-700">
                {{ $order->user->name ?? __('order.customer_name') }}<br>
                {{ $order->user->email ?? '' }}<br>
                {{ $order->address->address_line ?? __('order.address_fallback') }}
            </p>
        </div>

        <table class="w-full border-collapse mb-6 text-xs">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">{{ __('order.hash_col') }}</th>
                    <th class="border px-2 py-1">{{ __('order.product_col') }}</th>
                    <th class="border px-2 py-1">{{ __('order.variant_col') }}</th>
                    <th class="border px-2 py-1">{{ __('order.qty_col') }}</th>
                    <th class="border px-2 py-1">{{ __('order.unit_price_col') }}</th>
                    <th class="border px-2 py-1">{{ __('order.subtotal_col') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="border px-2 py-1">{{ $index + 1 }}</td>
                    <td class="border px-2 py-1">{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="border px-2 py-1">
                        {{ __('order.color_label') }} {{ $item->variant->color->name ?? '-' }}<br>
                        {{ __('order.size_label') }} {{ $item->variant->size ?? '-' }}
                    </td>
                    <td class="border px-2 py-1 text-center">{{ $item->quantity }}</td>
                    <td class="border px-2 py-1 text-right">₹ {{ number_format($item->price, 2) }}</td>
                    <td class="border px-2 py-1 text-right">₹ {{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mb-8">
            <p class="text-sm font-bold">{{ __('order.total_label') }} ₹ {{ number_format($order->total_amount, 2) }}</p>
        </div>

        <div class="text-center text-xs text-gray-500">{{ __('order.thank_you') }}</div>

        <div class="mt-6 text-center">
            <button onclick="window.print()" class="px-4 py-2 bg-rose-500 text-white rounded-xl font-bold text-xs hover:bg-rose-600 transition-all">{{ __('order.print_invoice') }}</button>
        </div>

    </div>
</div>
@endsection
