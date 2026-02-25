@extends('customer.layouts.app')

@section('title', 'Shop - Browse Collections')

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

<div class="bg-rose-50/20 min-h-screen py-10 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-900">Our Collection ✨</h1>
                <p class="text-gray-500 text-sm mt-1">Discover your next favorite outfit.</p>
            </div>

            {{-- search section --}}
            {{-- <div class="relative w-full md:w-80">
                <input type="text" placeholder="Search product name..."
                    class="w-full bg-white border border-rose-100 rounded-2xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all shadow-sm">
                <i class="fa-solid fa-magnifying-glass absolute right-5 top-1/2 -translate-y-1/2 text-rose-300"></i>
            </div> --}}
        </div>

        {{-- main section --}}
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- left section / Filter or preference section --}}
            {{-- <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-rose-50 sticky top-28">
                    <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2 text-sm uppercase tracking-wider">
                        Filter By Vendor 🏠
                    </h3>

                    <div class="space-y-3">
                        @foreach(['Zara Official', 'Urban Chic', 'Little Ones', 'Vintage Co.'] as $vendor)
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-rose-200 text-rose-500 focus:ring-rose-500">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-rose-500 transition-colors">{{ $vendor }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div class="h-[1px] bg-rose-50 my-8"></div>

                    <a href="#" class="text-[10px] font-black text-gray-400 hover:text-rose-500 transition-colors uppercase tracking-[0.2em]">
                        Reset Filters
                    </a>
                </div>
            </aside> --}}

            {{-- right section / Product Listing section --}}
            <div class="flex-grow">

                {{-- sub div (container) --}}
                @if($products->isEmpty())
                    {{-- 🌸 No Products Found --}}
                    <div class="bg-white border border-rose-100 rounded-3xl p-16 text-center shadow-sm">

                        <div class="text-6xl mb-6">🌸</div>

                        <h2 class="text-2xl font-black text-gray-800 mb-2">
                            No Products Found
                        </h2>

                        <p class="text-gray-500 text-sm mb-6">
                            Looks like this collection is waiting to be filled with something amazing ✨
                        </p>

                        <a href="{{ route('customer.shop') }}"
                           class="inline-block bg-rose-500 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-rose-200 hover:bg-rose-600 transition-all active:scale-95">
                            Browse All Products
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        {{-- iterate over products --}}
                        @foreach ($products as $product)
                        <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-rose-50 group">

                            <div class="aspect-[4/5] bg-rose-50 relative overflow-hidden flex items-center justify-center">

                                {{-- set image --}}
                                @php
                                    $imageUrl = $product->colors[0]?->getFirstMediaUrl('color_images');
                                @endphp

                                {{-- for image --}}
                                @if($imageUrl)
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $product->name ?? 'N/A' }}"
                                        class="w-full h-full object-cover"
                                    />
                                @else
                                    {{-- background when no image default --}}
                                    <div class="text-rose-200 group-hover:scale-110 transition-transform duration-700">
                                        <i class="fa-solid fa-shirt text-6xl"></i>
                                    </div>
                                @endif

                                {{-- wishlist button --}}
                                {{-- <button class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-400 hover:text-rose-500 transition-all">
                                    <i class="fa-regular fa-heart"></i>
                                </button> --}}

                                {{-- hover secton for cart btn --}}
                                {{-- <div class="absolute inset-0 bg-rose-900/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                    <button class="w-full bg-rose-500 text-white py-3.5 rounded-2xl font-bold shadow-xl translate-y-4 group-hover:translate-y-0 transition-all duration-300 flex items-center justify-center gap-2 active:scale-95">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        Add to Cart
                                    </button>
                                </div> --}}
                            </div>

                            <div class="p-6 text-center">
                                <a href="{{ route('customer.product.show', ['product' => $product]) }}"
                                    class="font-bold text-gray-800 mb-1 truncate text-lg">
                                    {{ $product->name ?? 'N/A' }}
                                </a>
                                <p class="text-rose-500 font-bold mt-1">₹ {{ $product->base_price ?? 0 }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- pagination --}}
                {{-- <div class="mt-16 flex justify-center items-center gap-4">
                    <button class="px-4 py-2 rounded-xl text-gray-400 font-bold hover:text-rose-500 transition-colors italic">Prev</button>
                    <span class="w-10 h-10 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold shadow-lg shadow-rose-200">1</span>
                    <button class="px-4 py-2 rounded-xl text-gray-400 font-bold hover:text-rose-500 transition-colors italic">Next</button>
                </div> --}}
            </div>

        </div>
    </div>
</div>
@endsection
