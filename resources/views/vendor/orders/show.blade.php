@extends('vendor.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="p-8 space-y-8">

    {{-- Header --}}
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Order #{{ $order->order_number }}
            </h1>
            <p class="text-sm text-gray-500">
                Placed on {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <a href="{{ route('vendor.orders.index') }}"
           class="text-sm font-semibold text-indigo-600 hover:underline">
            ← Back to Orders
        </a>
    </div>

    {{-- Toasts --}}
    @foreach (['success', 'error', 'info'] as $msg)
        @if(session($msg))
            <div class="p-4 rounded-xl text-sm font-semibold
                        {{ $msg === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : '' }}
                        {{ $msg === 'error' ? 'bg-orange-50 border border-orange-200 text-orange-700' : '' }}
                        {{ $msg === 'info' ? 'bg-blue-50 border border-blue-200 text-blue-700' : '' }}">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach
    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm font-semibold bg-red-50 border border-red-200 text-red-700 mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Customer Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">
            Customer Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">

            {{-- Left Column: Name, Email, Address --}}
            <div class="space-y-2">
                <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>

                @if($order->address)
                    <p><strong>Shipping Address:</strong></p>
                    <p class="text-gray-500 text-xs mt-1">
                        {{ $order->address->address_line ?? '' }}<br>
                        {{ $order->address->city ?? '' }}, {{ $order->address->state ?? '' }}<br>
                        {{ $order->address->pincode ?? '' }}
                    </p>
                @endif
            </div>

            {{-- Right Column: Payment Info --}}
            <div class="flex flex-col gap-2">
                <p>
                    <strong>Payment Mode:</strong> {{ ucfirst($order->payment_mode) }}
                </p>

                <p>
                    <strong>Payment Status:</strong>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-gray-100 text-gray-800'
                        ];
                        $paymentClass = $statusColors[strtolower($order->payment_status)] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $paymentClass }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>

        </div>
    </div>



    {{-- Vendor Items --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
        <h3 class="text-sm font-bold text-gray-800 mb-6 uppercase tracking-wider">
            Your Items in This Order
        </h3>

        @foreach($order->items as $item)
            <div class="border border-gray-100 rounded-xl p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4
                        {{ $item->order_status === 'cancelled' ? 'opacity-60 bg-gray-50' : '' }}">

                <div class="space-y-2">
                    <h4 class="font-semibold text-gray-900">
                        {{ $item->product->name ?? 'N/A' }}
                    </h4>

                    <p class="text-xs text-gray-500">
                        Qty: {{ $item->quantity ?? 'N/A' }} | Price: ₹ {{ number_format($item->price, 2) }}
                    </p>

                    {{-- Status Badge --}}
                    <p class="text-xs mt-1 font-semibold">
                        Current Status:
                        @php
                            $statusColors = [
                                'pending' => 'bg-gray-100 text-gray-700',
                                'processing' => 'bg-yellow-100 text-yellow-800',
                                'ready_for_pickup' => 'bg-orange-100 text-orange-800',
                                'shipped' => 'bg-blue-100 text-blue-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ];
                            $badgeClass = $statusColors[$item->order_status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs {{ $badgeClass }}">
                            {{ ucfirst($item->order_status) }}
                        </span>
                    </p>

                    {{-- Optional: Show subtotal per item --}}
                    @if($item->order_status !== 'cancelled')
                        <p class="text-xs text-gray-600 mt-1">
                            Subtotal: ₹ {{ number_format($item->quantity * $item->price, 2) }}
                        </p>
                    @endif
                </div>

                {{-- Status Update --}}
                @if(!in_array($item->order_status, ['cancelled', 'delivered']))
                    <form method="POST"
                          action="{{ route('vendor.orders.items.update-status', $item->id) }}"
                          class="flex items-center gap-3">
                        @csrf
                        @method('PUT')

                        <select name="order_status"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">

                            @if($item->order_status === 'pending')
                                <option value="processing">Mark as Processing</option>
                            @endif

                            @if($item->order_status === 'processing')
                                <option value="ready_for_pickup">Ready for Pickup</option>
                            @endif
                        </select>

                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                            Update
                        </button>
                    </form>
                @endif

                {{-- Status Cancel --}}
                @if(!in_array($item->order_status, ['delivered', 'cancelled']))

                    <div x-data="{ open: false }" class="mt-4">

                        <button @click="open = !open"
                            class="px-4 py-2 bg-red-500 text-white text-xs font-bold rounded-xl hover:bg-red-600 transition-all">
                            Cancel Item
                        </button>

                        <form x-show="open"
                              x-transition
                              action="{{ route('vendor.orders.cancel', $item->id) }}"
                              method="POST"
                              class="mt-3 space-y-3">
                            @csrf
                            @method('PATCH')

                            <textarea name="cancel_reason"
                                required
                                maxlength="255"
                                placeholder="Reason for cancellation..."
                                class="w-full border rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-300"></textarea>

                            <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-black">
                                Confirm Cancellation
                            </button>
                        </form>

                    </div>

                @endif

            </div>
        @endforeach

        {{-- Total Earnings --}}
        @php
            $totalVendorEarnings = $order->items->sum(fn($i) => $i->order_status !== 'cancelled' ? ($i->price * $i->quantity) : 0);
        @endphp
        <div class="mt-4 text-right text-sm font-semibold text-gray-800">
            Estimated Earnings: ₹ {{ number_format($totalVendorEarnings, 2) }}
        </div>

    </div>

</div>
@endsection
