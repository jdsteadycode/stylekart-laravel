@extends('delivery-person.layouts.app')


@section('content')

{{--new: toasts --}}
@if (session('success'))
    <div class="m-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="m-4 text-red-700 bg-red-100 px-4 py-2 rounded-md text-sm">
        {{ session('error') }}
    </div>
@endif

<div x-data="{ tab: 'available' }" class="max-w-5xl mx-auto p-6">

    {{-- Header --}}
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Delivery Dashboard</h1>
            <p class="text-slate-400 text-xs">Stylekart Logistics Hub</p>
        </div>
        <div class="text-right">
            <template x-if="tab === 'available'">
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 uppercase">● Online</span>
            </template>
        </div>
    </div>

    {{-- Minimal Tabs --}}
    <div class="flex gap-8 border-b border-slate-100 mb-8">
        <button @click="tab = 'available'" :class="tab === 'available' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400'" class="pb-4 border-b-2 font-bold text-sm transition-all">
            Available ({{ $availableOrders->count() }})
        </button>
        <button @click="tab = 'active'" :class="tab === 'active' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400'" class="pb-4 border-b-2 font-bold text-sm transition-all">
            Active Task
        </button>
        <button @click="tab = 'history'" :class="tab === 'history' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400'" class="pb-4 border-b-2 font-bold text-sm transition-all">
            History
        </button>
    </div>

    {{-- Tab 1: Available Jobs --}}
    <section x-show="tab === 'available'" x-transition.opacity>
        <div class="space-y-3">

            {{-- for available orders --}}
            @foreach ($availableOrders as $order)
                <div class="bg-white border border-slate-100 rounded-xl p-5 hover:shadow-md transition-all">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                        {{-- Clean Route: Pickup -> Drop --}}
                        <div class="flex-1">
                            <span class="text-[9px] font-black text-slate-300 uppercase">#{{ $order->order_number }}</span>
                            <p class="text-sm font-semibold text-slate-700 mt-0.5">
                                {{ $order->items->first()->vendor->addresses->first()->city ?? 'Store' }}
                                <span class="mx-2 text-indigo-300">→</span>
                                {{ $order->address->city }}
                            </p>
                        </div>

                        <div class="flex items-center gap-6">
                            {{-- <div class="text-right">
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Payout</p>
                                <p class="text-lg font-black text-slate-900">₹{{ number_format($order->total_amount, 2) }}</p>
                            </div> --}}
                            <a
                               href="{{ route('delivery.order.show', ['order'=>$order]) }}"
                                class="bg-slate-900 text-white text-[11px] font-bold px-5 py-2.5 rounded-lg hover:bg-slate-800 transition">
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- empty states --}}
            @if($availableOrders->count() < 1)
                <p
                    class="text-gray-400 bg-gray-50"
                >
                    Waiting for jobs...
                </p>
            @endif
        </div>
    </section>

    {{-- Tab 2: Active Task --}}
    <section x-show="tab === 'active'" x-transition.opacity style="display: none;">
        @forelse($acceptedOrders as $order)
            <div class="bg-white border border-indigo-100 rounded-2xl p-6 shadow-sm">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Active Task</span>
                        <h2 class="text-xl font-bold text-slate-800">{{ $order->user->name }}</h2>
                    </div>
                    <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-3 py-1 rounded-full border border-indigo-100 uppercase">
                        {{ str_replace('_', ' ', $order->order_status) }}
                    </span>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Destination</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $order->address->address_line }}, {{ $order->address->city }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Contact</p>
                        <p class="text-sm text-slate-700 font-bold">{{ $order->address->phone }}</p>
                    </div>
                </div>

                {{-- Action Area --}}
                <form action="{{ route('delivery.order.complete', $order->id) }}" method="POST" class="border-t pt-6">
                    @csrf
                    @if($order->order_status === 'shipped')
                        <input type="hidden" name="order_status" value="out_for_delivery">
                        <button class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">Pick up Parcel</button>
                    @elseif($order->order_status === 'out_for_delivery')
                        <input type="hidden" name="order_status" value="delivered">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" name="otp" maxlength="6" placeholder="Customer OTP" required class="flex-1 p-3 text-center text-lg font-black border rounded-xl focus:border-indigo-500 outline-none">
                            <button class="bg-emerald-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-emerald-700 transition">Confirm Delivery</button>
                        </div>
                    @endif
                </form>
            </div>
        @empty
            <div class="py-20 text-center text-slate-300 text-sm italic">No active task. Accept a job to begin.</div>
        @endforelse
    </section>

</div>
@endsection
