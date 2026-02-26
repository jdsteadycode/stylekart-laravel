@extends('customer.layouts.app')

{{-- some default code --}}
@php
    // get colors used by this variant
    $availableColors = $product->variants->pluck('color')->unique('id')->values();

    // get images from selected variant
    $images = $selectedVariant?->color?->getMedia('color_images');
@endphp

@section('title', 'Product Details')

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

<div class="bg-white min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <nav class="mb-8">
            <a href="{{ route('customer.shop') }}" class="text-[10px] font-black text-rose-400 uppercase tracking-widest flex items-center gap-2 hover:text-rose-600 transition-colors">
                <i class="fa-solid fa-chevron-left text-[8px]"></i> Back to Collection
            </a>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

            <div class="space-y-4">


                {{-- main image --}}
                <div class="aspect-[4/5] bg-rose-50 rounded-[40px] overflow-hidden border border-rose-50 flex items-center justify-center relative group">

                    @if($images && $images->count())
                        <img
                            id="mainImageEl"
                            src="{{ $images->first()->getUrl() }}"
                            class="w-full h-full object-cover"
                        />
                    @else
                        {{-- when no images --}}
                        <i class="fa-solid fa-shirt text-9xl text-rose-200"></i>
                    @endif
                </div>

                {{-- sub images --}}
                <div id="thumbnailContainer" class="flex gap-4">
                    @foreach($images as $image)
                        <div class="w-20 h-24 bg-rose-50 rounded-2xl border-2 border-rose-500 border-transparent flex items-center justify-center text-rose-200 cursor-pointer hover:border-rose-300 transition-all
                        ">
                            @if($image)
                                <img
                                    src="{{ $image->getUrl() }}"
                                    class="subImageEl w-full h-full object-cover border-2 rounded-2xl"

                                />
                            @else
                                <i class="fa-solid fa-image text-xl"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col h-full pt-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] bg-rose-50 px-3 py-1 rounded-full">
                        By {{ $product->vendor?->name . "'s STORE" ?? 'N/A' }} 🏠
                    </span>
                </div>

                <h1 class="text-4xl font-black text-gray-900 mb-4 leading-tight">
                   {{ $product->name ?? 'N/A'  }}
                   {{-- Premium Hand-Woven <br> Silk Blend Kurta --}}
                </h1>

                <div class="flex items-center gap-6 mb-8">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">
                        ₹ {{ $selectedVariant->price ?? $product->base_price ?? 0 }}
                    </span>

                    {{-- off section --}}
                    {{--
                    <span class="text-lg text-gray-400 line-through">₹4,999</span>
                    <span class="text-sm font-bold text-green-500 uppercase">50% Off ✨</span>
                    --}}

                    {{-- stock intel section --}}
                    @if($selectedVariant->stock > 0)
                    <div class="flex items-center gap-2 px-3 py-1 bg-amber-50 rounded-lg border border-amber-100">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest">
                            Only {{ $selectedVariant->stock }} Left
                        </span>
                    </div>
                    @else
                        <div class="flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-lg border border-gray-300">
                            <span class="relative flex h-2 w-2">
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
                            </span>
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                Out of Stock
                            </span>
                        </div>
                    @endif
                </div>

                <div class="space-y-10 mb-10">
                    <div>
                        <span class="text-xs font-black text-gray-900 uppercase tracking-widest block mb-4">
                            Select Color: <span class="text-rose-500 italic">
                                {{ $selectedVariant->color->name }}
                            </span></span>


                        {{-- selectable color section --}}
                        <div class="flex gap-4">

                            @foreach($availableColors as $color)
                                @php
                                    $firstVariantForColor = $product->variants()->where('color_id', $color->id)->first();
                                @endphp

                                <a
                                    href="{{ route('customer.product.show', ['product' => $product, 'variant' => $firstVariantForColor->id]) }}"
                                    class="color-btn w-10 h-10 rounded-full
                                    border-4 border-white
                                    {{ $selectedVariant->color_id == $color->id ? 'ring-2 ring-rose-500' : ''}}
                                    transition-all"
                                    style="background-color: {{ $color->name }}"
                                > </a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        {{-- size help section --}}
                        {{-- <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Select Size</span>
                            <button class="text-[10px] font-bold text-rose-400 underline uppercase tracking-widest">Size Guide</button>
                        </div> --}}

                        {{-- selectable sizes --}}
                        <div class="flex gap-3">
                            @foreach(
                                $product->variants
                                    ->where('color_id', $selectedVariant->color_id)
                                    ->unique('size')
                                as $variant
                            )
                                <a
                                    href="{{ route('customer.product.show', [
                                        'product' => $product,
                                        'variant' => $variant->id
                                    ]) }}"
                                    class="w-12 h-12 flex items-center justify-center rounded-xl border-2 {{ $selectedVariant->id === $variant->id ? 'bg-rose-400 text-white' : '' }} {{ $variant->stock < 1 ? 'cursor-not-allowed pointer-events-none opacity-40' : '' }}"
                                >
                                    {{ $variant->size }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-auto pt-6 border-t border-rose-50">


                    {{-- add to bag --}}
                    <form
                        action="{{ route('customer.cart.store') }}"
                        method="POST"
                        class="flex-[2]"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="variant_id"
                            value="{{ $selectedVariant->id }}"
                        />
                        <input
                            type="hidden"
                            name="qty"
                            value="1"
                        />
                        <button
                            class="
                                w-full bg-rose-500
                                text-white py-5
                                rounded-[24px]
                                font-black text-sm
                                uppercase tracking-widest
                                shadow-xl shadow-rose-100
                                hover:bg-rose-600 hover:-translate-y-1
                                transition-all active:scale-95
                                flex items-center justify-center
                                gap-3 disabled:bg-gray-300
                                disabled:text-gray-500 disabled:cursor-not-allowed"
                            {{ $selectedVariant->stock < 1 ? 'disabled' : '' }}
                        >
                            <i class="fa-solid fa-bag-shopping"></i>
                            {{ $selectedVariant->stock < 1 ? 'Out of Stock' : 'Add to Bag'}}
                        </button>

                    </form>

                    <button class="flex-1 bg-white border-2 border-rose-50 text-rose-500 py-5 rounded-[24px] font-black text-sm uppercase tracking-widest hover:bg-rose-50 transition-all flex items-center justify-center">
                        <i class="fa-regular fa-heart text-lg"></i>
                    </button>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-4 bg-rose-50/30 rounded-2xl">
                        <i class="fa-solid fa-truck-fast text-rose-400 text-xs"></i>
                        <span class="text-[10px] font-black text-gray-800 uppercase tracking-tighter">Fast Delivery</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-rose-50/30 rounded-2xl">
                        <i class="fa-solid fa-rotate-left text-rose-400 text-xs"></i>
                        <span class="text-[10px] font-black text-gray-800 uppercase tracking-tighter">Easy Returns</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>

    // () -> change image
    function changeImage(imageUrl) {
        // check..
        if(!imageUrl) {

            // log..
            console.log('no image url found to change');
            return;
        }

        // update the main image src..
        document.querySelector("#mainImageEl").src = imageUrl;
    }

    // attach click event to thumbnail container of child images..
    document.querySelector("#thumbnailContainer").onclick = function(event) {

        // check if subImage element was clicked..
        if(event.target.classList.contains("subImageEl")) {

            // check log..
            // console.log(event.target);

            // () -> change image..
            changeImage(event.target.src);
            return;
        }
    }
</script>
@endsection
