@extends('delivery-person.layouts.app')

@section('content')

{{-- Flash Messages --}}
@if (session('success'))
    <div class="m-4 text-emerald-700 bg-emerald-100 px-4 py-3 rounded-xl text-sm font-bold shadow-sm">
        <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="m-4 text-red-700 bg-red-100 px-4 py-3 rounded-xl text-sm font-bold shadow-sm">
        <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
    </div>
@endif

<div class="max-w-5xl mx-auto p-6">

    {{-- Header --}}
    <div class="mb-10 flex justify-between items-center border-b border-slate-100 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-rotate-left text-orange-500"></i> Reverse Pickups
            </h1>
            <p class="text-slate-500 text-sm mt-1 font-medium">Manage customer return logistics.</p>
        </div>
    </div>

    {{-- SECTION 1: THE ACTIVE TASK --}}
    @if($activeReturn)
        <div class="mb-12">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Current Active Task</h3>

            <div class="bg-white border-2 border-orange-200 rounded-3xl p-6 md:p-8 shadow-lg shadow-orange-100/50 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-400 to-red-400"></div>

                <div class="flex justify-between items-start mb-8">
                    <div>
                        <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest bg-orange-50 px-3 py-1 rounded-full">Return Job #{{ $activeReturn->id }}</span>
                    </div>
                    <span class="bg-slate-900 text-white text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
                        {{ str_replace('_', ' ', $activeReturn->status) }}
                    </span>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    @if($activeReturn->status === 'accepted')
                        {{-- Phase 1: Go to Customer --}}
                        <div class="space-y-2 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-black text-orange-500 uppercase tracking-wider">1. Head to Pickup</p>
                            <p class="text-lg font-bold text-slate-800">{{ $activeReturn->pickup_address['name'] ?? 'Customer' }}</p>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $activeReturn->pickup_address['address_line'] ?? '' }}, {{ $activeReturn->pickup_address['city'] ?? '' }}</p>

                            {{-- NEW: Map Link for Phase 1 --}}
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($activeReturn->pickup_address['address_line'] ?? '') . ' ' . ($activeReturn->pickup_address['city'] ?? '') . ' ' . ($activeReturn->pickup_address['pincode'] ?? '')) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-orange-600 hover:underline">
                                <span>📍 Open Navigation to Customer</span>
                            </a>
                        </div>
                        <div class="space-y-2 bg-slate-50 p-5 rounded-2xl border border-slate-100 flex flex-col justify-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Contact Customer</p>
                            <p class="text-xl text-slate-800 font-black"><i class="fa-solid fa-phone text-orange-400 mr-2"></i> {{ $activeReturn->pickup_address['phone'] ?? 'N/A' }}</p>
                        </div>
                    @else
                        {{-- Phase 2: Go to Vendor --}}
                        <div class="space-y-2 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">2. Head to Drop-off</p>
                            <p class="text-lg font-bold text-slate-800">{{ $activeReturn->dropoff_address['name'] ?? 'Vendor Shop' }}</p>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $activeReturn->dropoff_address['address_line'] ?? '' }}, {{ $activeReturn->dropoff_address['city'] ?? '' }}</p>

                            {{-- NEW: Map Link for Phase 2 --}}
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($activeReturn->dropoff_address['address_line'] ?? '') . ' ' . ($activeReturn->dropoff_address['city'] ?? '') . ' ' . ($activeReturn->dropoff_address['pincode'] ?? '')) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-indigo-600 hover:underline">
                                <span>🏁 Open Navigation to Store</span>
                            </a>
                        </div>
                        <div class="space-y-2 bg-slate-50 p-5 rounded-2xl border border-slate-100 flex flex-col justify-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Contact Store</p>
                            <p class="text-xl text-slate-800 font-black"><i class="fa-solid fa-phone text-indigo-400 mr-2"></i> {{ $activeReturn->dropoff_address['phone'] ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>

                {{-- Action Forms --}}
                <form action="{{ route('delivery.return.complete', $activeReturn->id) }}" method="POST">
                    @csrf
                    @if($activeReturn->status === 'accepted')
                        <input type="hidden" name="status" value="picked_up">
                        <button class="w-full bg-orange-500 text-white font-black py-4 rounded-2xl hover:bg-orange-600 transition shadow-lg shadow-orange-200 text-lg active:scale-[0.98]">
                            I Have Picked Up The Item
                        </button>
                    @elseif($activeReturn->status === 'picked_up')
                        <input type="hidden" name="status" value="completed">
                        <button class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl hover:bg-slate-800 transition shadow-lg shadow-slate-200 text-lg active:scale-[0.98]">
                            Confirm Drop-off at Store
                        </button>
                    @endif
                </form>
            </div>
        </div>
    @endif


    {{-- SECTION 2: AVAILABLE JOBS --}}
    <div>
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Available Pickups in your Area</h3>

        @if($activeReturn)
            {{-- NEW: Driver is busy, hide the list and show a warning! --}}
            <div class="py-12 text-center bg-orange-50/50 rounded-3xl border border-orange-100">
                <div class="text-4xl mb-4">🚧</div>
                <h4 class="text-slate-800 font-bold mb-1">Active Task in Progress</h4>
                <p class="text-slate-500 text-sm">Please finish your current accepted return before viewing or accepting new jobs.</p>
            </div>
        @else
            {{-- Driver is free, show the list! --}}
            <div class="space-y-4">
                @forelse ($availableReturns as $return)
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-orange-300 transition-all shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                            {{-- Route Info --}}
                            <div class="flex-1 w-full">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Job #{{ $return->id }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-slate-800">{{ $return->pickup_city }}</span>
                                    {{-- <span class="text-orange-400"><i class="fa-solid fa-arrow-right-long"></i></span> --}}
                                    <span class="text-orange-600">→</span>
                                    <span class="text-sm font-bold text-slate-800">{{ $return->dropoff_city }}</span>
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <a href="{{ route('delivery.return.show', ['job' => $return->id]) }}"
                               class="w-full md:w-auto text-center bg-orange-50 text-orange-600 border border-orange-200 text-xs font-bold px-6 py-3 rounded-xl hover:bg-orange-500 hover:text-white transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center bg-slate-50 rounded-3xl border border-slate-100">
                        <div class="text-4xl mb-4 opacity-50">📍</div>
                        <h4 class="text-slate-800 font-bold mb-1">No Reverse Pickups</h4>
                        <p class="text-slate-400 text-sm">There are no return requests in your city right now.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

</div>
@endsection
