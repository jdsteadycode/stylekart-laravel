@extends('customer.layouts.app')

@section('title', 'Order Failed')

@section('content')
<div class="bg-rose-50/20 min-h-screen py-20 flex items-center justify-center">
    <div class="max-w-md w-full px-6 text-center">

        <div class="mb-8 relative inline-block">
            <div class="w-24 h-24 bg-gray-900 rounded-[32px] flex items-center justify-center text-rose-500 text-4xl shadow-xl shadow-gray-200">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <div class="absolute -top-2 -right-2 text-2xl">🥀</div>
        </div>

        <h1 class="text-3xl font-black text-gray-900 mb-4">Transaction Failed</h1>
        <p class="text-gray-500 font-medium mb-10 leading-relaxed">
            Oops! Something went wrong with the payment. Don't worry, no money was deducted from your account.
        </p>

        <div class="bg-white p-6 rounded-[32px] border border-rose-50 shadow-sm mb-10 text-left">
            <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">What happened?</h4>
            <ul class="text-xs text-gray-500 space-y-2 font-medium">
                <li>• Payment gateway timeout</li>
                <li>• Insufficient funds or card decline</li>
                <li>• Incorrect OTP entered</li>
            </ul>
        </div>

        <div class="flex flex-col gap-4">
            <a href="{{ route('customer.checkout') }}" class="w-full bg-rose-500 text-white py-4 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-rose-100 hover:bg-rose-600 transition-all active:scale-95">
                Try Payment Again
            </a>
            <a href="{{ route('customer.cart.index') }}" class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-rose-500 transition-colors">
                Return to Cart
            </a>
        </div>

    </div>
</div>
@endsection
