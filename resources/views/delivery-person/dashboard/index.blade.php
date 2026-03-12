@extends('delivery-person.layouts.app')

@section('content')
<div x-data="{ tab: 'available' }" class="max-w-6xl mx-auto p-4 md:p-8">

    {{-- Page Header --}}
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Delivery Dashboard</h1>
            <p class="text-slate-500 mt-1">Real-time delivery management for StyleKart.</p>
        </div>

        {{-- Active badge: Busy / Free --}}
        {{-- <div class="hidden md:block">
            @if($availableOrders->isNotEmpty())
                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-bold border border-green-200 shadow-sm">
                    ● System: Accepting New Orders
                </span>
            @else
                <span class="bg-amber-100 text-amber-700 px-4 py-2 rounded-full text-sm font-bold border border-amber-200 shadow-sm">
                    ● System: Current Task Active
                </span>
            @endif
        </div> --}}
        <div class="hidden md:block">
            <template x-if="tab === 'available'">
                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-bold border border-green-200 shadow-sm animate-pulse">
                    ● Ready to Take Work
                </span>
            </template>
            <template x-if="tab === 'active'">
                <span class="bg-amber-100 text-amber-700 px-4 py-2 rounded-full text-sm font-bold border border-amber-200 shadow-sm">
                    ● Focus on Current Task
                </span>
            </template>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- tabs switches --}}
    <div class="flex p-1 bg-slate-200 rounded-2xl mb-8 w-fit">
        <button @click="tab = 'available'"
            :class="tab === 'available' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600'"
            class="px-8 py-3 rounded-xl text-sm font-black transition-all duration-200 uppercase tracking-wider">
            Available ({{ $availableOrders->count() }})
        </button>
        <button @click="tab = 'active'"
            :class="tab === 'active' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600'"
            class="px-8 py-3 rounded-xl text-sm font-black transition-all duration-200 uppercase tracking-wider">
            Accepted ({{ $acceptedOrders->count() }})
        </button>
    </div>

    {{-- available orders --}}
    <section x-show="tab === 'available'" x-transition>
        @if($availableOrders->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center gap-2 mb-4">
                    <span class="h-2 w-2 rounded-full bg-blue-600 animate-ping"></span>
                    <h2 class="text-xl font-bold text-slate-800">New Available Orders</h2>
                </div>
                <div class="grid gap-4">
                    @foreach($availableOrders as $order)
                        <div class="bg-indigo-50/30 border-2 border-dashed border-indigo-200 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center gap-4 transition hover:border-indigo-400">
                            <div class="text-center md:text-left">
                                <span class="text-xs font-bold text-indigo-600 uppercase">#{{ $order->order_number }}</span>
                                <p class="text-lg font-bold text-slate-800">{{ $order->address->city ?? 'N/A' }} ({{ $order->address->pincode }})</p>
                                <p class="text-sm text-slate-500 line-clamp-1">{{ $order->address->address_line }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-bold text-slate-700">₹{{ number_format($order->total_amount, 2) }}</span>
                                <form action="{{ route('delivery.order.accept', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl font-bold transition shadow-md hover:shadow-indigo-200">
                                        Accept Order
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white p-12 rounded-3xl text-center border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-bold italic text-lg">Waiting for new orders from vendors...</p>
            </div>
        @endif
    </section>

    {{-- accepted order--}}
    <section x-show="tab === 'active'" x-transition>
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4">My Current Deliveries</h2>
            <div class="grid gap-6">
                @forelse($acceptedOrders as $order)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">

                        {{-- Card Header --}}
                        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                            <div>
                                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Active Order #{{ $order->order_number }}</span>
                                <h2 class="text-xl font-bold text-slate-800 mt-1">{{ $order->user->name }}</h2>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border bg-blue-100 text-blue-700 border-blue-200">
                                {{ strtoupper($order->order_status) }}
                            </span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6 grid md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-slate-100 rounded-lg text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase">Delivery Address</p>
                                        <p class="text-sm text-slate-700 font-medium leading-relaxed">
                                            {{ $order->address->address_line }},<br>
                                            {{ $order->address->city }} - {{ $order->address->pincode }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-slate-100 rounded-lg text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase">Customer Phone</p>
                                        <p class="text-sm text-slate-700 font-medium">{{ $order->address->phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col justify-end">
                                <form action="{{ route('delivery.order.complete', $order->id) }}" method="POST">
                                    @csrf
                                    @if($order->order_status === 'shipped')
                                        <input type="hidden" name="order_status" value="out_for_delivery">
                                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                            <span>📦 Pick up Parcel</span>
                                        </button>
                                    @elseif($order->order_status === 'out_for_delivery')
                                        <input type="hidden" name="order_status" value="delivered">
                                        <input type="text" name="otp" maxlength="6" placeholder="Enter 6-digit OTP" required
                                               class="w-full mb-3 p-3 text-center text-xl font-black tracking-[0.5em] border-2 border-slate-200 rounded-xl focus:border-emerald-500 outline-none transition-all">
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                            <span>✅ Confirm Delivery</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-3xl text-center border-2 border-dashed border-slate-200">
                        <p class="text-slate-500 italic">Accept one to start delivery!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
