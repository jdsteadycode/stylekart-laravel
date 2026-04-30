@extends('customer.layouts.app')

@section('title', 'My Cart')

@php
    $bag = session('bag', []);
    $subTotal = 0;
@endphp

@section('content')
<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-10 text-center md:text-left">
            <h1 class="text-3xl font-black text-gray-900">{{ __('cart.title') }} <span class="text-rose-500">{{ __('cart.items_count', ['count' => count($bag)]) }}</span></h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <div class="lg:col-span-2 space-y-4">

                @if(count($bag))
                @foreach($bag as $item)

                        @php
                            // Fetch the fresh variant from the DB to get the live discounted price
                            $variant = \App\Models\ProductVariant::find($item['variant_id']);
                            $livePrice = $variant ? $variant->selling_price : $item['price'];
                            $subTotal += ($item['qty'] * $livePrice);
                        @endphp

                        <div class="bg-white p-5 rounded-3xl border border-rose-50 shadow-sm flex items-center gap-6 group">
                            <!-- Product Image -->
                            <div class="w-20 h-24 bg-rose-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-rose-200">
                                <i class="fa-solid fa-shirt text-2xl"></i>
                            </div>

                            <!-- Product Info -->
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $item['product_name'] }}</h3>
                                    <form
                                        action="{{ route('customer.cart.destroy', $item['variant_id']) }}"
                                        method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-gray-300 hover:text-rose-500 transition-colors text-sm">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                    </form>
                                </div>

                                <!-- Variant Details -->
                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-500 font-medium">
                                    <span>{{ __('cart.size') }} <span class="font-bold text-gray-700">{{ $item['size'] ?? 'N/A' }}</span></span>
                                    <span>{{ __('cart.color') }}
                                        <span class="inline-block w-3 h-3 rounded-full border border-gray-200"
                                              style="background-color: {{ $item['color'] ?? '#ccc' }}"></span>
                                        <span class="ml-1 font-bold text-gray-700">{{ $item['color'] ?? 'N/A' }}</span>
                                    </span>
                                    <span>{{ __('cart.stock_left') }} <span class="font-bold text-rose-500">{{ $item['stock'] ?? 0 }}</span></span>
                                </div>

                                <!-- Quantity & Price -->
                                <div class="flex justify-between items-center mt-4">
                                    <form action="{{ route('customer.cart.update', $item['variant_id']) }}" method="POST" class="flex items-center gap-3 bg-rose-50/50 rounded-xl p-1 px-2 border border-rose-50">
                                        @csrf
                                        @method('PATCH')
                                        <button name="action" value="decrease" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-rose-500 font-bold">-</button>
                                        <span class="text-sm font-black text-gray-700">{{ $item['qty'] }}</span>
                                        <button name="action" value="increase" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-rose-500 font-bold">+</button>
                                    </form>

                                    <span class="text-lg font-black text-gray-900">₹{{ number_format($livePrice * $item['qty'], 0) }}</span>
                                </div>
                            </div>
                        </div>

                   @endforeach
                @else
                    <p class="text-gray-500 text-center">{{ __('cart.empty_bag') }}</p>
                @endif

                <div class="pt-4">
                    <a href="{{ route('customer.shop') }}" class="text-sm font-bold text-rose-400 hover:text-rose-600 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-xs"></i> {{ __('cart.continue_shopping') }}
                    </a>
                </div>
            </div>

            {{-- bag summary --}}
            <div class="lg:col-span-1">

                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm sticky top-28">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">{{ __('cart.summary_title') }}</h3>

                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between text-gray-500 font-medium">
                            <span>{{ __('cart.subtotal') }}</span>
                            <span class="text-gray-900">₹ {{ $subTotal }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500 font-medium">
                            <span>{{ __('cart.shipping') }}</span>
                            <span class="text-green-500 font-bold italic">{{ __('cart.free') }}</span>
                        </div>

                        <div class="border-t border-rose-50 pt-4 mt-4 flex justify-between">
                            <span class="text-base font-bold text-gray-900">{{ __('cart.total_amount') }}</span>
                            <span class="text-xl font-black text-rose-500">₹ {{ $subTotal }}</span>
                        </div>
                    </div>

                    {{-- checkout button --}}
                    @if(count($bag) > 0)
                    <a href="{{ route('customer.checkout') }}"
                           class="block w-full text-center bg-rose-500 text-white py-4 rounded-2xl font-bold mt-8 shadow-lg shadow-rose-100 hover:bg-rose-600 active:scale-95 transition-all">
                            {{ __('cart.checkout_now') }}
                        </a>
                    @else
                        <button disabled
                            class="w-full bg-gray-300 text-white py-4 rounded-2xl font-bold mt-8 cursor-not-allowed">
                            {{ __('cart.checkout_now') }}
                        </button>
                    @endif

                    <div class="mt-6 flex justify-center gap-4 text-gray-300">
                        <i class="fa-brands fa-cc-visa text-xl"></i>
                        <i class="fa-brands fa-cc-mastercard text-xl"></i>
                        <i class="fa-solid fa-shield-halved text-lg"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
