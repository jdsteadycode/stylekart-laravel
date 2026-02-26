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

        <h1 class="text-3xl font-black text-gray-900 mb-4">Yay! Order Placed ✨</h1>
        <p class="text-gray-500 font-medium mb-10 leading-relaxed">
            Your style journey has begun! We've sent the order details to your email. Get ready to look fabulous.
        </p>

        <div class="bg-white p-6 rounded-3xl border border-rose-50 shadow-sm mb-10">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Order ID</span>
                <span class="text-sm font-bold text-gray-800">#{{ $order->order_number ?? 'XXXX' }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Paid</span>
                <span class="text-lg font-black text-rose-500">₹{{ $order->total_amount ?? 0 }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <a href="{{ route('customer.profile') }}" class="w-full bg-rose-500 text-white py-4 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-rose-100 hover:bg-rose-600 transition-all active:scale-95">
                Track My Order
            </a>
            <a href="{{ route('customer.shop') }}" class="text-[11px] font-black text-rose-400 uppercase tracking-[0.2em] hover:text-rose-600 transition-colors">
                Continue Shopping
            </a>
        </div>

    </div>
</div>

@endsection
