@extends('customer.layouts.app')

@section('title', 'Shop - Browse Collections')

@section('content')
{{-- Toast for add to bag --}}
@if(session('success') || session('error') || $errors->any())
    <div
        id="toast"
        class="fixed top-6 right-6 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-bold transition-all duration-500"
        style="background-color: {{ session('success') ? '#16a34a' : '#dc2626' }};"
    >
        @if(session('success'))
            🛍️ {{ session('success') }}
        @elseif(session('error'))
            ⚠️ {{ session('error') }}
        @else
            ⚠️ {{ $errors->first() }}
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
                <h1 class="text-3xl font-black text-gray-900">{{ __('shop.title') }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ __('shop.subtitle') }}</p>
            </div>

            {{-- search section --}}
            <div class="relative w-full md:w-80 mb-6 md:mb-0">
                <form method="GET" action="{{ route('customer.shop') }}">

                    {{-- if brand filter exists --}}
                    @if(request('brand'))
                        @foreach((array) request('brand') as $brandId)
                            <input type="hidden" name="brand[]" value="{{ $brandId }}">
                        @endforeach
                    @endif

                    {{-- if category filter --}}
                    @if(request('category'))
                        <input
                            name="category"
                            value="{{ request('category') }}"
                            type="hidden"
                        />
                    @endif

                    {{-- if prices to be filtered! --}}
                    @if(request('min_price'))
                        <input
                            name="min_price"
                            value="{{ request('min_price') }}"
                            type="hidden"
                        />
                    @endif
                    @if(request('max_price'))
                        <input
                            name="max_price"
                            value="{{ request('max_price') }}"
                            type="hidden"
                        />
                    @endif

                    {{-- if vendor filter --}}
                    @if(request('vendor'))
                        @foreach((array) request('vendor') as $vendorId)
                            <input
                                name="vendor[]"
                                value="{{ $vendorId }}"
                                type="hidden"
                            />
                        @endforeach
                    @endif

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('shop.search_placeholder') ?? 'Search product name...' }}"
                        class="w-full bg-white border border-rose-100 rounded-2xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all shadow-sm"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-rose-400 hover:text-rose-500">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- main section --}}
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- left section / Filter or preference section --}}
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-rose-50 sticky top-28">

                    <form method="GET" action="{{ route('customer.shop') }}" class="space-y-6">

                        {{-- if search is also there --}}
                        @if(request('search'))
                            <input
                                name="search"
                                value="{{ request('search') }}"
                                type="hidden"
                            />
                        @endif

                        {{-- By Category --}}
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            {{ __('shop.filter_category') }}
                        </h3>

                        @foreach($allCategories as $category)
                            <label class="flex items-center group cursor-pointer">
                                <input
                                    type="radio"
                                    name="category"
                                    value="{{ $category->id }}"
                                    class="w-4 h-4 rounded border-rose-200 text-rose-500 focus:ring-rose-500"
                                    {{ request('category') == $category->id ? 'checked' : '' }}
                                >
                                <span class="ml-3 text-sm text-gray-600 group-hover:text-rose-500 transition-colors">{{ $category->name }}</span>
                            </label>
                        @endforeach


                        {{-- By Brand --}}
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            {{ __('shop.filter_brand') }}
                        </h3>

                        <div class="space-y-2 mb-6">
                            @foreach($allBrands as $brand)
                                <label class="flex items-center group cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="brand[]"
                                        value="{{ $brand->slug }}"
                                        class="w-4 h-4 rounded border-rose-200 text-rose-500 focus:ring-rose-500"
                                        {{ in_array($brand->slug, (array) request('brand')) ? 'checked' : '' }}
                                    >
                                    <span class="ml-3 text-sm text-gray-600 group-hover:text-rose-500 transition-colors">
                                        {{ $brand->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        {{-- By Vendor --}}
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            {{ __('shop.filter_vendor') }}
                        </h3>

                        @foreach($allVendors as $vendor)
                            <label class="flex items-center group cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="vendor[]"
                                    value="{{ $vendor->id }}"
                                    class="w-4 h-4 rounded border-rose-200 text-rose-500 focus:ring-rose-500"
                                    {{ in_array($vendor->id, (array) request('vendor')) ? 'checked' : '' }}
                                >
                                <span class="ml-3 text-sm text-gray-600 group-hover:text-rose-500 transition-colors">{{ $vendor->name }}</span>
                            </label>
                        @endforeach

                        {{-- By Price --}}
                        <h3 class="font-bold text-gray-800 mb-4 mt-6 flex items-center gap-2 text-sm uppercase tracking-wider">
                            {{ __('shop.filter_price') }}
                        </h3>

                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">₹</span>
                                <input
                                    type="number"
                                    name="min_price"
                                    value="{{ request('min_price') }}"
                                    placeholder="Min"
                                    class="w-full pl-7 pr-3 py-2 text-sm border border-rose-100 rounded-xl focus:ring-2 focus:ring-rose-300 outline-none transition-all shadow-sm"
                                    min="0"
                                >
                                </div>
                                <span class="text-gray-400 font-bold">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">₹</span>
                                    <input
                                        type="number"
                                        name="max_price"
                                        value="{{ request('max_price') }}"
                                        placeholder="Max"
                                        class="w-full pl-7 pr-3 py-2 text-sm border border-rose-100 rounded-xl focus:ring-2 focus:ring-rose-300 outline-none transition-all shadow-sm"
                                        min="0"
                                    >
                            </div>
                        </div>

                        <button type="submit" class="mt-4 w-full bg-rose-500 text-white py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-rose-600 transition-all active:scale-95">
                            {{ __('shop.apply') }}
                        </button>

                    </form>

                    <div class="h-[1px] bg-rose-50 my-8"></div>
                    <a href="{{ route('customer.shop') }}" class="text-[10px] font-black text-gray-400 hover:text-rose-500 transition-colors uppercase tracking-[0.2em]">
                        {{ __('shop.reset_filters') }}
                    </a>

                </div>
            </aside>


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

                                {{-- if product has live discount going on --}}
                                {{-- sale live badge (simple) --}}
                                {{-- @if($product->getActiveDiscount())
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="bg-rose-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg uppercase tracking-widest animate-pulse">
                                                Sale Live
                                        </span>
                                    </div>
                                @endif --}}

                                {{-- discount badge --}}
                                @if($discount = $product->getActiveDiscount())
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="bg-rose-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg uppercase tracking-widest">
                                            @if($discount->discount_type === 'percentage')
                                                🎉 {{ $discount->percentage }}% OFF
                                            @elseif($discount->discount_value)
                                            💸 ₹{{ $discount->discount_value }} OFF
                                            @endif
                                        </span>
                                    </div>

                                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10">
                                        <div class="bg-white/60 backdrop-blur-sm px-3 py-1 rounded-md border border-white/30 shadow-sm flex items-center gap-2">
                                            <span class="text-s">⏰</span> {{-- better emoji/icon --}}
                                            <span
                                                class="countdown-timer text-xs font-medium text-neutral-600 tracking-wide"
                                                      data-ends-at="{{ $discount->ends_at->toIso8601String() }}">
                                                    00h 00m 00s
                                            </span>
                                        </div>
                                    </div>
                                @endif


                                {{-- set image --}}
                                @php
                                    $imageUrl = $product->colors?->first()?->getFirstMediaUrl('color_images');
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
                            </div>

                            {{-- product name --}}
                            <div class="p-6 text-center">
                                <a href="{{ route('customer.product.show', ['product' => $product]) }}"
                                    class="font-bold text-gray-800 mb-1 truncate text-lg">
                                    {{ $product->name ?? 'N/A' }}
                                </a>

                                {{-- product price --}}
                                {{-- <p class="text-rose-500 font-bold mt-1">₹ {{ $product->base_price ?? 0 }}</p> --}}
                                <div class="mt-1">
                                    <x-customer.product-price :product="$product" />
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- pagination --}}
                <div
                    class="mt-16 flex justify-center items-center"
                >
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    function updateMinimalTimers() {
        document.querySelectorAll('.countdown-timer').forEach(timer => {
            const endsAt = new Date(timer.dataset.endsAt).getTime();
            const now = new Date().getTime();
            const diff = endsAt - now;

            if (diff <= 0) {
                timer.closest('.z-10').remove(); // Hide completely if expired
                return;
            }

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff / (1000 * 60)) % 60);

            timer.innerHTML = `${h.toString().padStart(2, '0')}h ${m.toString().padStart(2, '0')}m`;
        });
    }

    // Update every minute is enough for h/m format
    setInterval(updateMinimalTimers, 60000);
    updateMinimalTimers();
</script>
@endsection
