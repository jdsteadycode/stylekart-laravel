@extends('customer.layouts.app')

@section('title', 'Checkout')

@php
    $bag = session()->get('bag', []);
@endphp

@section('content')

{{-- global error --}}
@if(session('error'))
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold">
        {{ session('error') }}
    </div>
@endif


<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <form action="{{ route('customer.checkout.placeOrder') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

                <div class="lg:col-span-2 space-y-6">

                    {{-- addresses section --}}
                    <div class="bg-white p-8 rounded-[32px] border border-rose-50 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center text-white text-xs">1</div>
                                <h3 class="text-xl font-black text-gray-900">Shipping Details</h3>
                            </div>


                            {{-- tab switcher for addresses section --}}
                            {{-- <div class="flex items-center gap-2 bg-rose-50 p-1.5 rounded-xl border border-rose-100">
                                <button class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest bg-white text-rose-500 shadow-sm">New Address</button>
                                <button class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-rose-500 transition-all">Use Saved</button>
                            </div> --}}
                        </div>

                        {{-- address form --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- name --}}
                            <input type="text" placeholder="Full Name" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all"
                                value="{{ old('name', $address?->name ?? '') }}"
                                name="name"
                            >
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror


                            {{-- phone --}}
                            <input type="text" placeholder="Phone Number" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all"
                                value="{{ old('phone', $address?->phone ?? '') }}"
                                name="phone"
                            >

                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            {{-- Address detail --}}
                            <div class="md:col-span-2">
                                <textarea rows="3" placeholder="Full Address (House No, Building, Area)" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all" name="address_line">{{ old('address_line', $address?->address_line ?? '')}}</textarea>
                                @error('address_line')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- City --}}
                            <input type="text" placeholder="City" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all" value="{{ old('city', $address?->city ?? '') }}" name="city">
                            @error('city')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror


                            {{-- Pincode --}}
                            <input type="text" placeholder="Pincode" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all" value="{{ old('pincode', $address?->pincode ?? '') }}" name="pincode">
                            @error('pincode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- payment mode section --}}
                    <div class="bg-white p-8 rounded-[32px] border border-rose-50 shadow-sm">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center text-white text-xs">2</div>
                            <h3 class="text-xl font-black text-gray-900">Payment Mode</h3>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4">
                            <label class="flex-1 flex items-center justify-between p-4 cursor-pointer rounded-2xl border border-rose-100 bg-rose-50/20 group hover:bg-rose-50 transition-all has-[:checked]:ring-2 has-[:checked]:ring-rose-500 has-[:checked]:bg-white">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-credit-card text-rose-400"></i>
                                    <span class="text-sm font-bold text-gray-700">Online Payment</span>
                                </div>
                                <input type="radio" name="pay" class="w-4 h-4 accent-rose-500" value="online"
                                    {{ old('pay', ' ') === 'online' ? 'checked' : ''}}
                                >
                            </label>

                            <label class="flex-1 flex items-center justify-between p-4 cursor-pointer rounded-2xl border border-rose-100 bg-rose-50/20 group hover:bg-rose-50 transition-all has-[:checked]:ring-2 has-[:checked]:ring-rose-500 has-[:checked]:bg-white">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-hand-holding-dollar text-rose-400"></i>
                                    <span class="text-sm font-bold text-gray-700">Cash on Delivery</span>
                                </div>
                                <input type="radio" name="pay" class="w-4 h-4 accent-rose-500" value="cod"
                                    {{ old('pay', ' ') === 'cod' ? 'checked' : ''}}
                                >
                            </label>

                            @error('pay')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Final Summary for Checkout --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-8 rounded-[32px] border border-rose-50 shadow-sm sticky top-28">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Order Summary</h3>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 font-medium">Subtotal ({{ count($bag); }} items)</span>
                                <span class="text-sm font-black text-gray-900">₹ {{ $subTotal }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 font-medium">Delivery Fee</span>
                                <span class="text-[10px] font-black text-green-500 uppercase">Free</span>
                            </div>
                            <div class="pt-4 border-t border-rose-50 flex justify-between items-center">
                                <span class="font-black text-gray-900">Total Payable</span>
                                <span class="text-2xl font-black text-rose-500">₹ {{ $subTotal }}</span>
                            </div>
                        </div>

                        {{-- checkout btn --}}
                        <button
                            class="w-full bg-rose-500 text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-rose-100 hover:bg-rose-600 active:scale-95 transition-all
                                   {{ count($bag) < 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ count($bag) < 1 ? 'disabled' : '' }}
                        >
                            Place Order
                        </button>



                        <div class="mt-6 flex justify-center gap-2">
                            <i class="fa-solid fa-shield-check text-rose-200 text-xs"></i>
                            <p class="text-[9px] text-gray-300 font-bold uppercase tracking-widest">Secure Checkout</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
