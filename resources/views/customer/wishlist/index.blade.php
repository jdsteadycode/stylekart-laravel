@extends('customer.layouts.app')

@section('title', 'My Wishlist')

@section('content')
{{-- Toast for add to bag --}}
@if(session('success') || session('error'))
    <div
        id="toast"
        class="fixed top-6 right-6 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-bold transition-all duration-500"
        style="background-color: {{ session('success') ? '#16a34a' : '#dc2626' }};"
    >
        @if(session('success'))
            🛍️ {{ session('success') }}
        @else
            ⚠️ {{ session('error') }}
        @endif
    </div>

    <script>
        // Hide toast after 3 seconds
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-[-20px]');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
    </script>
@endif

<div class="bg-[#fffafb] min-h-screen py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-12 text-center">
            <span class="text-[10px] font-black text-rose-400 uppercase tracking-[0.3em] mb-2 block italic">{{ __('wishlist.your_collection') }}</span>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ __('wishlist.saved_for_later') }} <span class="text-rose-500">🌸</span></h1>
            <p class="text-gray-400 text-xs mt-2 font-medium">{{ __('wishlist.subtitle') }}'</p>
        </div>

        @if($wishlistedItems->count() < 1)
            {{-- Empty State --}}
            <div class="max-w-md mx-auto text-center py-20 bg-white rounded-[40px] border border-rose-50 shadow-sm">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl">🥀</span>
                </div>
                <h2 class="text-xl font-black text-gray-800 tracking-tight">{{ __('wishlist.resting_title') }}</h2>
                <p class="text-gray-400 text-xs mt-2 mb-8 px-10 leading-relaxed font-medium uppercase tracking-tighter">{{ __('wishlist.resting_desc') }}</p>
                <a href="{{ route('customer.shop') }}" class="inline-block bg-rose-500 text-white px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-rose-100 hover:bg-rose-600 transition-all active:scale-95">{{ __('wishlist.explore_shop') }}</a>
            </div>
        @else
            {{-- Wishlist Section --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($wishlistedItems as $item)
                    <div class="group relative bg-white rounded-[32px] overflow-hidden border border-rose-50/50 shadow-sm hover:shadow-xl hover:shadow-rose-100/50 transition-all duration-500">

                        {{-- Delete from Wishlist --}}
                        <form method="POST" action="{{ route('customer.wishlist.destroy', ['item' => $item]) }}" class="absolute top-4 right-4 z-20">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 bg-white/90 backdrop-blur-md rounded-xl flex items-center justify-center text-gray-300 hover:text-rose-500 hover:rotate-90 transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </form>

                        {{-- Variant Image --}}
                        <div class="aspect-[4/5] bg-[#fdf8f9] relative overflow-hidden flex items-center justify-center">
                            @php
                                $variantImage = $item->variant->color?->getMedia('color_images')->first();
                            @endphp
                            @if($variantImage)
                                <img src="{{ $variantImage->getUrl() }}"
                                     alt="{{ $item->product->name }} - {{ $item->variant->size }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="text-center">
                                    <i class="fa-solid fa-shirt text-5xl text-rose-100 group-hover:scale-110 transition-transform duration-500"></i>
                                    <p class="text-[8px] font-black text-rose-200 uppercase tracking-widest mt-3">{{ __('wishlist.no_preview') }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Product + Variant Details --}}
                        <div class="p-6 text-center">
                            <a href="{{ route('customer.product.show', ['product' => $item->product, 'variant' => $item->variant->id]) }}"
                               class="font-black text-gray-800 truncate text-sm tracking-tight mb-1 group-hover:text-rose-500 transition-colors block">{{ $item->product?->name ?? __('wishlist.untitled_style') }}</a>

                            {{-- Variant info --}}
                            <div class="flex items-center justify-center gap-2 mb-3">
                                {{-- Size --}}
                                <span class="text-xs font-bold text-gray-500 uppercase">{{ __('wishlist.size_label') }} {{ $item->variant->size }}</span>

                                {{-- Color --}}
                                @if($item->variant->color)
                                    <span class="w-4 h-4 rounded-full border border-gray-200"
                                          style="background-color: {{ $item->variant->color->name }}">
                                    </span>
                                    <span class="text-xs font-bold text-gray-500 uppercase">{{ $item->variant->color->name }}</span>
                                @endif
                            </div>

                            {{-- Variant Price --}}
                            <p class="text-lg font-black text-gray-900 mb-5 italic">₹{{ number_format($item->variant->price ?? $item->product->base_price ?? 0, 2) }}</p>

                            {{-- Move to Bag --}}
                            <form action="{{ route('customer.cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="variant_id" value="{{ $item->variant->id }}">
                                <input type="hidden" name="qty" value="1">
                                <button class="w-full py-4 bg-rose-50 text-rose-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-rose-500 hover:text-white transition-all active:scale-95 shadow-sm">{{ __('wishlist.move_to_bag') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
