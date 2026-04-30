@extends('customer.layouts.app')

@section('title', 'Order Successful')

@section('content')
<div class="bg-rose-50/20 min-h-screen py-20 flex items-center justify-center">
    <div class="max-w-md mx-auto px-6 text-center">

        <div class="relative inline-block mb-8">
            <div class="w-24 h-24 bg-green-500 rounded-3xl flex items-center justify-center text-white text-4xl shadow-xl shadow-green-100 animate-bounce">
                <i class="fa-solid fa-check"></i>
            </div>
            {{-- <div class="absolute -top-2 -right-2 text-2xl"></div> --}}
        </div>

        <h1 class="text-3xl font-black text-gray-900 mb-4">{{ __('checkout.yay_placed') }}</h1>
        <p class="text-gray-500 font-medium mb-10 leading-relaxed">{{ __('checkout.success_msg') }}</p>

        <div class="bg-white p-6 rounded-3xl border border-rose-50 shadow-sm mb-10 space-y-3">
            {{-- Order ID --}}
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('checkout.order_id') }}</span>
                <span class="text-sm font-bold text-gray-800">#{{ $order->order_number ?? 'XXXX' }}</span>
            </div>

            {{-- Wallet Breakdown (Shows only when the wallet was actually tapped) --}}
            @if($order->wallet_amount_used > 0)
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('checkout.order_total') }}</span>
                    <span class="text-sm font-bold text-gray-800">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ __('checkout.wallet_used') }}</span>
                    <span class="text-sm font-bold text-emerald-600">- ₹{{ number_format($order->wallet_amount_used, 2) }}</span>
                </div>
                <div class="border-t border-rose-50 my-1"></div>
            @endif

            {{-- The Dynamic Label --}}
            <div class="flex justify-between items-center pt-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    {{ $order->payment_status === 'paid' ? __('checkout.total_paid') : __('checkout.remaining_pay') }}
                </span>
                <span class="text-lg font-black text-rose-500">
                    ₹{{ number_format($order->payable_amount, 2) }}
                </span>
            </div>

            {{-- Payment Method Tag --}}
            <div class="text-right pt-2">
                <span class="text-[8px] font-black bg-rose-50 text-rose-400 px-2 py-1 rounded-md uppercase tracking-widest">
                    {{ __('checkout.via') }} {{ strtoupper($order->payment_mode) }}
                </span>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <a href="{{ route('customer.profile') }}" class="w-full bg-rose-500 text-white py-4 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-rose-100 hover:bg-rose-600 transition-all active:scale-95">
                {{ __('checkout.track_order') }}
            </a>
            <a href="{{ route('customer.shop') }}" class="text-[11px] font-black text-rose-400 uppercase tracking-[0.2em] hover:text-rose-600 transition-colors">
                {{ __('checkout.continue_shopping') }}
            </a>
        </div>

    </div>
</div>

@endsection
