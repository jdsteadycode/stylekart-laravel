@extends('delivery-person.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    {{-- Back Link --}}
    <a href="{{ route('dashboard.delivery') }}" class="text-xs font-bold text-slate-400 hover:text-indigo-600 flex items-center gap-2 mb-8 transition">
        ← Back to orders
    </a>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        {{-- Header Section --}}
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Order Opportunity</span>
            <h2 class="text-3xl font-black text-slate-900 mt-2">₹{{ number_format($order->total_amount, 2) }}</h2>
            <p class="text-slate-500 text-sm mt-1">Order #{{ $order->order_number }}</p>
        </div>

        {{-- The Route Visual --}}
        <div class="p-8 space-y-10">
            {{-- Pickup --}}
            <div class="flex gap-6">
                <div class="flex flex-col items-center">
                    <div class="h-6 w-6 rounded-full border-2 border-orange-400 flex items-center justify-center bg-orange-50">
                        <div class="h-2 w-2 rounded-full bg-orange-400"></div>
                    </div>
                    <div class="h-16 border-l-2 border-dashed border-slate-100"></div>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-wider mb-1">Pickup From</p>
                    <p class="text-lg font-bold text-slate-800">{{ $order->items->first()->vendor->vendorProfile->shop_name ?? 'Vendor Shop' }}</p>

                    <div class="mt-2 text-sm text-slate-500 leading-relaxed">
                        <p>{{ $order->items->first()->vendor->addresses->first()->address_line }}</p>
                        <p class="font-bold text-slate-700">
                            {{ $order->items->first()->vendor->addresses->first()->city }},
                            {{ $order->items->first()->vendor->addresses->first()->state ?? 'Gujarat' }}
                        </p>
                    </div>

                    {{-- Simple Google Maps Link --}}
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->items->first()->vendor->addresses->first()->address_line . ' ' . $order->items->first()->vendor->addresses->first()->city) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-orange-600 hover:underline">
                        <span>📍 View Pickup on Maps</span>
                    </a>
                </div>
            </div>

            {{-- Drop-off --}}
            <div class="flex gap-6">
                <div class="h-6 w-6 rounded-full border-2 border-indigo-600 flex items-center justify-center bg-indigo-50">
                    <div class="h-2 w-2 rounded-full bg-indigo-600"></div>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-wider mb-1">Deliver To</p>
                    <p class="text-lg font-bold text-slate-800">{{ $order->user->name }}</p>

                    <div class="mt-2 text-sm text-slate-500 leading-relaxed">
                        <p>{{ $order->address->address_line }}</p>
                        <p class="font-bold text-slate-700">
                            {{ $order->address->city }},
                            {{ $order->address->state ?? 'Gujarat' }}
                        </p>
                    </div>

                    {{-- Simple Google Maps Link --}}
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->address->address_line . ' ' . $order->address->city) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-indigo-600 hover:underline">
                        <span>🏁 View Drop-off on Maps</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Action Area --}}
        <div class="p-8 bg-slate-50 border-t border-slate-100">
            <form action="{{ route('delivery.order.accept', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-lg hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-[0.98]">
                    Accept Delivery Job
                </button>
            </form>
            <p class="text-center text-[10px] text-slate-400 mt-4 font-medium uppercase tracking-widest">
                By accepting, you commit to fulfilling this delivery immediately.
            </p>
        </div>
    </div>
</div>
@endsection
