@extends('customer.layouts.app')

@section('content')
<div class="relative bg-gradient-to-r from-rose-50 to-pink-100 py-20 lg:py-32 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="md:w-2/3 lg:w-1/2">
            <span class="inline-block px-4 py-1.5 mb-4 text-xs font-bold tracking-widest text-rose-600 uppercase bg-rose-200 rounded-full">
                New Season Arrived 🌸
            </span>
            <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 leading-tight mb-6">
                Discover Your <span class="text-rose-500">True Style</span>
            </h1>
            <p class="text-lg text-gray-600 mb-10 leading-relaxed">
                Shop from hundreds of independent vendors and boutiques all in one place. Fresh, unique, and hand-picked just for you.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('customer.shop') }}" class="bg-rose-500 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-rose-200 hover:bg-rose-600 hover:-translate-y-1 transition-all duration-300">
                    Shop Now 🛍️
                </a>
                <a href=" {{ route('register', ['role' => 'vendor']) }} " class="bg-white text-rose-500 border-2 border-rose-100 px-8 py-4 rounded-2xl font-bold hover:bg-rose-50 transition-all duration-300">
                    Become a Vendor 🏪
                </a>
            </div>
        </div>
    </div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-rose-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
</div>

{{-- categories section --}}
<div class="max-w-7xl mx-auto py-20 px-6">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div>
            <h2 class="text-4xl font-black text-gray-900">Choose Your Vibe 🌈</h2>
            <p class="text-gray-500 mt-2">Curated collections for everyone.</p>
        </div>
    </div>

    {{-- categories sub-section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @php
            // for some default categories,
            $data = [
                'Women' => [
                    'emoji' => '💃', 'color' => 'bg-pink-100', 'text' => 'text-pink-700'
                ],
                'Men' => [
                    'emoji' => '👔', 'color' => 'bg-blue-100', 'text' => 'text-blue-700'
                ],
                'Kids' => [
                    'emoji' => '🧸', 'color' => 'bg-yellow-100', 'text' => 'text-yellow-700'
                ]
            ] ;
        @endphp

        {{-- iterate over categories.. --}}
        @foreach($categories as $cat)

            @php
                // default data when new category doesn't match existing..
                $ui = $data[$cat->name] ?? [
                    'emoji' => '🛍️',
                    'color' => 'bg-gray-100',
                    'text'  => 'text-gray-700'
                ];
            @endphp

            <div class="group relative overflow-hidden rounded-3xl
                {{ $ui['color'] }}
                p-8 h-80 flex flex-col justify-between
                hover:shadow-2xl hover:shadow-rose-100
                transition-all duration-500 cursor-pointer">
                <div class="text-6xl">{{ $ui['emoji'] }}</div>
                <div>
                    <h3 class="text-3xl font-bold {{ $ui['text'] }}">{{ $cat->name }}</h3>
                    <a
                        href="{{ route('customer.shop' , ['category' => $cat->id]) }}"
                        class="mt-2 font-medium {{ $ui['text'] }} opacity-80">Explore Collection →</a>
                </div>
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white opacity-20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            </div>
        @endforeach
    </div>
</div>

{{-- vendors section --}}
<div class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Shop from Top Vendors 🏢</h2>
            <p class="text-gray-500 mt-2">Support local creators and global brands.</p>
        </div>

        {{-- <div class="flex flex-wrap justify-center gap-12 grayscale opacity-60 hover:grayscale-0 transition-all">
            <div class="flex items-center space-x-2 font-black text-2xl text-gray-400 italic">ZARA-ISH</div>
            <div class="flex items-center space-x-2 font-black text-2xl text-gray-400 italic">URBAN-TREND</div>
            <div class="flex items-center space-x-2 font-black text-2xl text-gray-400 italic">LITTLE-ONES</div>
            <div class="flex items-center space-x-2 font-black text-2xl text-gray-400 italic">VINTAGE-CO</div>
        </div> --}}
    </div>
</div>

{{-- trending products section --}}
<div class="max-w-7xl mx-auto py-20 px-6 border-t border-rose-50">
    <h2 class="text-3xl font-bold text-gray-900 mb-10">Trending Now 🔥</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl p-3 border border-gray-100 hover:border-rose-200 transition-colors group">
            <div class="aspect-square bg-gray-50 rounded-xl mb-4 overflow-hidden relative">
                <img
                    src="{{ $product->colors[0]->getFirstMediaUrl('color_images') }}"
                    alt="{{ $product->name }}"
                    {{-- class="w-full h-full object-cover" --}}
                />

                {{-- wishlist icon --}}
                {{-- <button class="absolute top-2 right-2 p-2 bg-white/80 backdrop-blur-sm rounded-full text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fa-regular fa-heart"></i>
                </button> --}}
            </div>
            {{-- <p class="text-[10px] font-bold text-rose-400 uppercase tracking-tighter">Vendor Name</p> --}}
            <a
                href="{{ route('customer.product.show', ['product' => $product]) }}"
                class="font-semibold text-gray-800 text-sm">{{ $product->name ?? 'N/A' }}</a>
            <p class="text-rose-500 font-bold mt-1">RS {{ $product->base_price ?? 'N/A' }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
