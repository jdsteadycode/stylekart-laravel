@extends('vendor.layouts.app')

@section('title', 'Incoming Orders')

@section('content')
<div class="p-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Incoming Orders</h1>
        <p class="text-sm text-gray-500">Manage customer orders for your products</p>
    </div>

    {{-- toast section --}}
    {{-- for success --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200
                    text-green-700 rounded-xl text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    {{-- for error --}}
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200
            text-red-700 rounded-xl text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif
    {{-- for info --}}
    @if(session('info'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200
            text-blue-700 rounded-xl text-sm font-semibold">
            {{ session('info') }}
        </div>
    @endif


    {{-- order overall statuses section --}}
    <div class="mb-6 flex flex-wrap gap-4">

        @php
            $processingCount = $orders->sum(fn($o) => $o->items->where('order_status', 'processing')->count());
            $shippedCount = $orders->sum(fn($o) => $o->items->where('order_status', 'shipped')->count());
            $deliveredCount = $orders->sum(fn($o) => $o->items->where('order_status', 'delivered')->count());
            $pendingCount = $orders->sum(fn($o) => $o->items->where('order_status', 'pending')->count());
            $readyForPickupCount = $orders->sum(fn($o) => $o->items->where('order_status', 'ready_for_pickup')->count());
            $cancelledCount = $orders->sum(fn($o) => $o->items->where('order_status', 'cancelled')->count());
        @endphp

        <a
            href="{{ route('vendor.orders.index') }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ !request()->query('status') ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-800'}}">
            All
        </a>

        <a
            href="{{ route('vendor.orders.index', ['status' => 'pending']) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ request()->query('status') === 'pending' ? 'bg-slate-500 text-white' : 'bg-slate-100 text-slate-800'}}">
            Pending: {{ $pendingCount }}    {{-- Fixed: backgrond color for pending order label --}}
        </a>


        <a
            href="{{ route('vendor.orders.index', ['status' => 'processing']) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ request()->query('status') === 'processing' ? 'bg-yellow-500 text-yellow-100' : 'bg-yellow-100 text-yellow-800'}}">
            Processing: {{ $processingCount }}
        </a>

        {{-- New: Added ready-for-pickup orders --}}
        <a
            href="{{ route('vendor.orders.index', ['status' => 'ready_for_pickup']) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ request()->query('status') === 'ready_for_pickup' ? 'bg-orange-500 text-orange-100' : 'bg-orange-100 text-orange-800'}}">
            Ready for pickup: {{ $readyForPickupCount }}
        </a>

        <a
            href="{{ route('vendor.orders.index', ['status' => 'shipped']) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ request()->query('status') === 'shipped' ? 'bg-blue-500 text-blue-100' : 'bg-blue-100 text-blue-800'}}">
            Shipped: {{ $shippedCount }}
        </a>

        <a
            href="{{ route('vendor.orders.index', ['status' => 'delivered']) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ request()->query('status') === 'delivered' ? 'bg-green-500 text-green-100' : 'bg-green-100 text-green-800'}}">
            Delivered: {{ $deliveredCount }}
        </a>

        <a
            href="{{ route('vendor.orders.index', ['status' => 'cancelled']) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm {{ request()->query('status') === 'cancelled' ? 'bg-red-500 text-red-100' : 'bg-red-100 text-red-800'}}">
            Cancelled: {{ $cancelledCount }}
        </a>

    </div>


    {{-- check orders! --}}
    @if($orders->count())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">ORDER #</th>
                        <th class="px-6 py-4 text-left">ITEMS</th>
                        <th class="px-6 py-4 text-left">STATUS</th>
                        <th class="px-6 py-4 text-left">PLACED ON</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-semibold">
                                <a href="{{ route('vendor.orders.show', $order) }}"
                                   class=" hover:text-indigo-800 hover:underline transition">
                                    #{{ $order->order_number }}
                                </a>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $order->items->count() }} item(s)
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($order->order_status === 'pending') bg-gray-100 text-gray-800
                                    @elseif($order->order_status === 'processing') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status === 'ready_for_pickup') bg-orange-100 text-orange-800  {{-- New: Added style for ready for pickup order label --}}
                                    @elseif($order->order_status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($order->order_status === 'partially_fulfilled') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                </span>

                            </td>


                            <td class="px-6 py-4 text-gray-500">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>

        {{-- pagination links --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>

    @else
        <div class="p-10 text-center bg-white border border-gray-100 rounded-2xl flex flex-col items-center justify-center gap-4">
            <h1 class="text-gray-500 text-lg font-medium">📦 No orders yet.</h1>
            <p class="text-gray-400 text-sm">Orders from customers will appear here once they place them.</p>
        </div>
 @endif

</div>
@endsection
