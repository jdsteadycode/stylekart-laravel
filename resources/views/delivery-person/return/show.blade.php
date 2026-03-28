@extends('delivery-person.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    {{-- Back Link --}}
    <a href="{{ route('delivery.return.index') }}" class="text-xs font-bold text-slate-400 hover:text-orange-500 flex items-center gap-2 mb-8 transition">
        ← Back to jobs
    </a>

    <div class="bg-white rounded-3xl border border-orange-100 shadow-sm overflow-hidden relative">
        {{-- Header Section (No payments, just clear instructions) --}}
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-400 to-red-400"></div>
        <div class="p-8 border-b border-orange-50 bg-orange-50/30">
            <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest"><i class="fa-solid fa-rotate-left mr-1"></i> Reverse Pickup Task</span>
            <h2 class="text-2xl font-black text-slate-900 mt-2">Return Job #{{ $job->id }}</h2>
            <p class="text-slate-500 text-sm mt-1">Please pick up this item from the customer.</p>
        </div>

        {{-- The Route Visual --}}
        <div class="p-8 space-y-10">
            {{-- Pickup (CUSTOMER) --}}
            <div class="flex gap-6">
                <div class="flex flex-col items-center">
                    <div class="h-6 w-6 rounded-full border-2 border-orange-400 flex items-center justify-center bg-orange-50">
                        <div class="h-2 w-2 rounded-full bg-orange-400"></div>
                    </div>
                    <div class="h-16 border-l-2 border-dashed border-slate-200"></div>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-wider mb-1">1. Pickup From Customer</p>
                    <p class="text-lg font-bold text-slate-800">{{ $job->pickup_address['name'] ?? 'Customer' }}</p>

                    <div class="mt-2 text-sm text-slate-500 leading-relaxed">
                        <p class="font-bold text-slate-700 mb-1"><i class="fa-solid fa-phone text-xs mr-1 text-slate-400"></i> {{ $job->pickup_address['phone'] ?? 'N/A' }}</p>
                        <p>{{ $job->pickup_address['address_line'] ?? '' }}</p>
                        <p>{{ $job->pickup_address['city'] ?? '' }}, {{ $job->pickup_address['state'] ?? '' }} - {{ $job->pickup_address['pincode'] ?? '' }}</p>
                        @if(!empty($job->pickup_address['landmark']))
                            <p class="mt-1 text-xs text-orange-600 bg-orange-50 inline-block px-2 py-1 rounded">Landmark: {{ $job->pickup_address['landmark'] }}</p>
                        @endif
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode($job->pickup_address['address_line']. ' ' . $job->pickup_address['city'] . ' ' . $job->pickup_address['pincode']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-orange-600 hover:underline">
                            <span>📍 View Pickup on Maps</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Drop-off (VENDOR) --}}
            <div class="flex gap-6">
                <div class="h-6 w-6 rounded-full border-2 border-indigo-600 flex items-center justify-center bg-indigo-50">
                    <div class="h-2 w-2 rounded-full bg-indigo-600"></div>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-wider mb-1">2. Deliver To Store</p>
                    <p class="text-lg font-bold text-slate-800">{{ $job->dropoff_address['name'] ?? 'Vendor Shop' }}</p>

                    <div class="mt-2 text-sm text-slate-500 leading-relaxed">
                        <p class="font-bold text-slate-700 mb-1"><i class="fa-solid fa-phone text-xs mr-1 text-slate-400"></i> {{ $job->dropoff_address['phone'] ?? 'N/A' }}</p>
                        <p>{{ $job->dropoff_address['address_line'] ?? '' }}</p>
                        <p>{{ $job->dropoff_address['city'] ?? '' }}, {{ $job->dropoff_address['state'] ?? '' }} - {{ $job->dropoff_address['pincode'] ?? '' }}</p>
                        @if(!empty($job->dropoff_address['landmark']))
                            <p class="mt-1 text-xs text-orange-600 bg-orange-50 inline-block px-2 py-1 rounded">Landmark: {{ $job->dropoff_address['landmark'] }}</p>
                        @endif
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode($job->dropoff_address['address_line']. ' ' . $job->dropoff_address['city'] . ' ' . $job->dropoff_address['pincode']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-orange-600 hover:underline">
                            <span>📍 View DropOff on Maps</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Area --}}
        <div class="p-8 bg-slate-50 border-t border-slate-100">
            <form action="{{ route('delivery.return.accept', $job->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-orange-500 text-white py-4 rounded-2xl font-bold text-lg hover:bg-orange-600 transition-all shadow-xl shadow-orange-200 active:scale-[0.98]">
                    Accept Return Job
                </button>
            </form>
            <p class="text-center text-[10px] text-slate-400 mt-4 font-medium uppercase tracking-widest">
                By accepting, you commit to picking this item up from the customer.
            </p>
        </div>
    </div>
</div>
@endsection
