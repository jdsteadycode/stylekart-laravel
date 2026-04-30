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

@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold">
            {{ $error }}
        </div>
    @endforeach
@endif

<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <form action="{{ route('customer.checkout.placeOrder') }}" method="POST" x-data="{
                mode: '{{ old('use_new_address') == '1' || old('name') ? 'new' : ($addresses->isNotEmpty() ? 'saved' : 'new') }}',
                selectedAddress: '{{ old('address_id', $addresses->where('is_default', 1)->first()?->id ?? $addresses->first()?->id) }}',
                subTotal: {{ $subTotal }},
                walletBalance: {{ $walletBalance }},
                useWallet: false,
                get walletUsed() { return this.useWallet ? Math.min(this.subTotal, this.walletBalance) : 0 },
                get payable() { return this.subTotal - this.walletUsed }
            }">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

                <div class="lg:col-span-2 space-y-6">

                    {{-- addresses section --}}
                    <div

                        class="bg-white p-8 rounded-[32px] border border-rose-50 shadow-sm">

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center text-white text-xs font-bold">1</div>
                                <h3 class="text-xl font-black text-gray-900">{{ __('checkout.shipping_details') }}</h3>
                            </div>

                            {{-- Address Choice Tab --}}
                            @if($addresses->isNotEmpty())
                            <div class="flex items-center gap-2 bg-rose-50 p-1 rounded-xl border border-rose-100">
                                <button type="button" @click="mode = 'saved'" :class="mode === 'saved' ? 'bg-white text-rose-500 shadow-sm' : 'text-gray-400'" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">{{ __('checkout.saved') }}</button>
                                <button type="button" @click="mode = 'new'" :class="mode === 'new' ? 'bg-white text-rose-500 shadow-sm' : 'text-gray-400'" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">{{ __('checkout.new') }}</button>
                            </div>
                            @endif
                        </div>

                        {{-- Saved Address Section --}}
                        <div x-show="mode === 'saved'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($addresses as $addr)
                                <label class="relative flex flex-col p-4 cursor-pointer rounded-2xl border-2 transition-all hover:bg-rose-50/30"
                                    :class="selectedAddress == '{{ $addr->id }}' ? 'border-rose-500 bg-white ring-1 ring-rose-500' : 'border-rose-50 bg-rose-50/10'">

                                    <input type="radio" name="address_id" value="{{ $addr->id }}" class="hidden" x-model="selectedAddress" :disabled="mode === 'new'"
                                    >

                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-sm font-black text-gray-900">{{ $addr->name }}</span>
                                        <span x-show="selectedAddress == '{{ $addr->id }}'" class="text-[9px] font-black text-rose-500 uppercase">{{ __('checkout.selected') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                                        {{ $addr->address_line }}, {{ $addr->city }} - {{ $addr->pincode }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-2">📞 {{ $addr->phone }}</p>
                                </label>
                            @endforeach
                        </div>

                        {{-- New Address Section --}}
                        <div x-show="mode === 'new'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <input type="text" name="name" :disabled="mode === 'saved'" placeholder="{{ __('checkout.full_name') }}" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:ring-2 focus:ring-rose-300 outline-none" value="{{ old('name') }}">
                                    @error('name') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <input type="text" name="phone" :disabled="mode === 'saved'" placeholder="{{ __('checkout.phone') }}" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:ring-2 focus:ring-rose-300 outline-none" value="{{ old('phone') }}">
                                    @error('phone') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <textarea name="address_line" :disabled="mode === 'saved'" rows="3" placeholder="{{ __('checkout.address') }}" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:ring-2 focus:ring-rose-300 outline-none">{{ old('address_line') }}</textarea>
                                    @error('address_line') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <input type="text" name="city" :disabled="mode === 'saved'" placeholder="{{ __('checkout.city') }}" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:ring-2 focus:ring-rose-300 outline-none" value="{{ old('city') }}">
                                    @error('city') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <input type="text" name="pincode" :disabled="mode === 'saved'" placeholder="{{ __('checkout.pincode') }}" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:ring-2 focus:ring-rose-300 outline-none" value="{{ old('pincode') }}">
                                    @error('pincode') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <input type="text" name="state" :disabled="mode === 'saved'" placeholder="{{ __('checkout.state') }}" class="w-full bg-rose-50/30 border border-rose-50 rounded-xl px-5 py-3 text-sm focus:ring-2 focus:ring-rose-300 outline-none" value="{{ old('state') }}">
                                    @error('state') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">{{ __('checkout.address_type') }}</label>
                                    <select name="address_type" :disabled="mode === 'saved'" class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all">
                                        <option value="Where to?" hidden>{{ __('checkout.where_to') }}</option>
                                        <option value="home" {{ old('address_type') === 'home' ? 'selected' : ''}}>{{ __('checkout.home_delivery') }}</option>
                                        <option value="office" {{ old('address_type') === 'office' ? 'selected' : ''}}>{{ __('checkout.office_delivery') }}</option>
                                        <option value="other" {{ old('address_type') === 'other' ? 'selected' : ''}}>{{ __('checkout.other') }}</option>
                                    </select>
                                    @error('address_type') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                                </div>

                            </div>
                    </div>

                    <div class="bg-white p-8 rounded-[32px] border border-rose-50 shadow-sm mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-black text-gray-900">{{ __('checkout.wallet_title') }}</h3>
                            <span class="text-emerald-600 font-bold">₹ {{ number_format($walletBalance, 2) }}</span>
                        </div>

                        @if($walletBalance > 0)
                        <label class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 cursor-pointer">
                            <input type="checkbox" name="use_wallet" x-model="useWallet" class="w-5 h-5 accent-emerald-500">
                            <span class="text-sm font-bold text-gray-700">{{ __('checkout.use_wallet') }} ({{ __('checkout.save_text') }} ₹<span x-text="walletUsed"></span>)</span>
                        </label>
                        @endif
                    </div>

                    {{-- payment mode section --}}
                    <div class="bg-white p-8 rounded-[32px] border border-rose-50 shadow-sm">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center text-white text-xs">2</div>
                            <h3 class="text-xl font-black text-gray-900">{{ __('checkout.payment_mode') }}</h3>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4">
                            <label class="flex-1 flex items-center justify-between p-4 cursor-pointer rounded-2xl border border-rose-100 bg-rose-50/20 group hover:bg-rose-50 transition-all has-[:checked]:ring-2 has-[:checked]:ring-rose-500 has-[:checked]:bg-white">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-credit-card text-rose-400"></i>
                                    <span class="text-sm font-bold text-gray-700">{{ __('checkout.online') }}</span>
                                </div>
                                <input type="radio" name="pay"
                                    class="w-4 h-4 accent-rose-500 text-rose-500 focus:outline-none focus:ring-0"
                                    value="online"
                                    {{ old('pay', ' ') === 'online' ? 'checked' : ''}}
                                >
                            </label>

                            <label class="flex-1 flex items-center justify-between p-4 cursor-pointer rounded-2xl border border-rose-100 bg-rose-50/20 group hover:bg-rose-50 transition-all has-[:checked]:ring-2 has-[:checked]:ring-rose-500 has-[:checked]:bg-white">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-hand-holding-dollar text-rose-400"></i>
                                    <span class="text-sm font-bold text-gray-700">{{ __('checkout.cod') }}</span>
                                </div>
                                <input type="radio" name="pay"
                                    class="w-4 h-4 accent-rose-500 text-rose-500 focus:outline-none focus:ring-0"
                                    value="cod"
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
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">{{ __('checkout.summary') }}</h3>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 font-medium">{{ __('checkout.subtotal', ['count' => count($bag)]) }}</span>
                                <span class="text-sm font-black text-gray-900">₹ {{ $subTotal }}</span>
                            </div>
                            {{-- <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 font-medium">{{ __('checkout.delivery') }}</span>
                                <span class="text-[10px] font-black text-green-500 uppercase">Free</span>
                            </div> --}}
                            <div class="pt-4 border-t border-rose-50 flex justify-between items-center">
                                <span class="font-black text-gray-900">{{ __('checkout.payable') }}</span>
                                <span class="text-2xl font-black text-rose-500">₹ <span x-text="payable"></span></span>
                            </div>
                        </div>

                        {{-- checkout btn --}}
                        <button
                            class="w-full bg-rose-500 text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-rose-100 hover:bg-rose-600 active:scale-95 transition-all
                                   {{ count($bag) < 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ count($bag) < 1 ? 'disabled' : '' }}
                        >
                            {{ __('checkout.place_order') }}
                        </button>



                        <div class="mt-6 flex justify-center gap-2">
                            <i class="fa-solid fa-shield-check text-rose-200 text-xs"></i>
                            <p class="text-[9px] text-gray-300 font-bold uppercase tracking-widest">{{ __('checkout.secure') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
