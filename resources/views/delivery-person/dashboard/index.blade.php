@extends('delivery-person.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 md:p-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">My Delivery Tasks</h1>
        <p class="text-slate-500 mt-1">Manage and track your active deliveries today.</p>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 animate-pulse">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 text-orange-700 rounded-xl flex items-center gap-3 animate-pulse">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid gap-6">
        @forelse($orders as $order)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">

                {{-- Card Header --}}
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Order #{{ $order->order_number }}</span>
                        <h2 class="text-xl font-bold text-slate-800 mt-1">{{ $order->user->name }}</h2>
                    </div>

                    {{-- Dynamic Badge --}}
                    @php
                        $statusStyles = [
                            'shipped' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'out_for_delivery' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'delivered' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        ];
                        $currentStyle = $statusStyles[$order->order_status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $currentStyle }}">
                        {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                    </span>
                </div>

                {{-- Card Body --}}
                <div class="p-6 grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-slate-100 rounded-lg text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Delivery Address</p>
                                <p class="text-sm text-slate-700 font-medium leading-relaxed">
                                    {{ $order->address->address_line ?? 'N/A' }},<br>
                                    {{ $order->address->city ?? 'N/A' }} - {{ $order->address->pincode ?? '' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-100 rounded-lg text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Customer Phone</p>
                                <p class="text-sm text-slate-700 font-medium">{{ $order->address->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Button Container --}}
                    <div class="flex flex-col justify-end">
                        <form action="{{ route('delivery.order.complete', $order->id) }}" method="POST">
                            @csrf
                            @method('POST')

                            @if($order->order_status === 'shipped')
                                <input type="hidden" name="order_status" value="out_for_delivery">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                                    <span>📦 Pick up Parcel</span>
                                </button>
                            @elseif($order->order_status === 'out_for_delivery')

                                {{-- new order-status --}}
                                <input type="hidden" name="order_status" value="delivered">

                                {{-- customer's otp --}}
                                <input
                                    type="text"
                                    name="otp" maxlength="6"
                                    placeholder="Enter 6-digit OTP" required
                                    class="w-full mb-3 p-3 text-center text-xl font-black tracking-[0.5em] border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-0 outline-none transition-all"
                                >
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-2">
                                    <span>✅ Confirm Delivery</span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-16 rounded-3xl text-center border-2 border-dashed border-slate-200">
                <div class="inline-flex p-4 bg-slate-50 rounded-full mb-4">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No active tasks</h3>
                <p class="text-slate-500">Wait for the admin to assign you new deliveries.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
