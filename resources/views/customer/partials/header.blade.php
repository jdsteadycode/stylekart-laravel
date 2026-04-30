<header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-rose-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

            <div class="flex items-center">
                <a href="{{ route('customer.home') }}" class="flex items-center space-x-2 group">
                    <div class="bg-rose-500 p-2 rounded-xl group-hover:rotate-12 transition-transform">
                        <span class="text-white text-xl">🛍️</span>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-800">
                        Style<span class="text-rose-500">kart</span>
                    </span>
                </a>
            </div>

            <div class="hidden md:flex space-x-10 items-center">
                <a href="{{ route('customer.home') }}" class="text-sm font-bold text-gray-600 hover:text-rose-500 transition-colors">{{ __('header.home') }}</a>

                <div class="relative group">
                    <button class="text-sm font-bold text-gray-600 hover:text-rose-500 flex items-center gap-1">
                        {{ __('header.categories') }} <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div class="absolute top-full -left-4 w-48 bg-white shadow-xl rounded-2xl p-2 border border-rose-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all mt-2">
                        @php
                            $emojiMap = [
                                "Men" => "👔",
                                "Women" => "💃",
                                "Kids" => "🧸"
                            ];
                        @endphp

                        {{-- iterate over categories --}}
                        @foreach($menuCategories as $category)
                            @php
                                $emoji = $emojiMap[$category->name] ?? '🛍️';
                            @endphp
                            <a href="{{ route('customer.shop', ['category' => $category->id]) }}" class="block px-4 py-2 hover:bg-rose-50 rounded-xl text-sm font-medium text-gray-700">{{ $emoji }} {{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('customer.shop') }}" class="text-sm font-bold text-gray-600 hover:text-rose-500 transition-colors">{{ __('header.shop') }}</a>
            </div>

            <div class="flex items-center space-x-3">

                {{-- wishlist --}}
                <a href="{{ route('customer.wishlist.index') }}" class="relative p-2 text-gray-400 hover:text-rose-500 transition-colors">
                    <i class="fa-regular fa-heart text-xl"></i>
                    <span class="absolute top-1 right-0 bg-rose-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border-2 border-white">
                        {{ $wishlistItemsCount ?? '0' }}
                    </span>
                </a>

                {{-- bag --}}
                <a href="{{ route('customer.cart.index') }}" class="relative p-2 text-gray-600 hover:text-rose-500 transition-colors group">
                    <i class="fa-solid fa-bag-shopping text-xl"></i>
                    <span class="absolute top-1 right-0 bg-rose-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border-2 border-white">
                        {{ count(session()->get('bag', [])) }}
                    </span>
                </a>

                <div class="h-6 w-[1px] bg-gray-200 mx-1 hidden md:block"></div>

                {{-- NEW: Language Selector Dropdown --}}
                <div class="relative group hidden md:block">
                    <button class="flex items-center gap-1 p-2 text-sm font-bold text-gray-600 hover:text-rose-500 transition-colors">
                        <i class="fa-solid fa-globe"></i>
                        {{ strtoupper(Session::get('locale', 'en')) }}
                    </button>
                    <div
                        class="absolute top-full right-0 w-24 bg-white shadow-xl rounded-xl p-2 border border-rose-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all mt-1">
                        <a href="{{ route('customer.lang.switch', 'en') }}"
                            class="block px-3 py-2 text-xs font-bold text-gray-700 hover:bg-rose-50 rounded-lg {{ Session::get('locale', 'en') === 'en' ? 'text-rose-500 bg-rose-50/50' : '' }}">English</a>
                        <a href="{{ route('customer.lang.switch', 'gu') }}"
                            class="block px-3 py-2 text-xs font-bold text-gray-700 hover:bg-rose-50 rounded-lg {{ Session::get('locale') === 'gu' ? 'text-rose-500 bg-rose-50/50' : '' }}">ગુજરાતી</a>
                    </div>
                </div>

                <div class="h-6 w-[1px] bg-gray-200 mx-1"></div>


                {{-- auth section & profile --}}
                @if(auth()->user())
                    {{-- show profile icon --}}
                    <a href="{{ route('customer.profile') }}" class="p-2 text-gray-400 hover:text-rose-500 transition-colors">
                        <i class="fa-regular fa-user text-xl"></i>
                    </a>

                    <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>

                    {{-- allow logout --}}
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf
                        <button
                            class="bg-rose-500
                            text-white
                            px-5 py-2.5
                            rounded-2xl font-bold
                            text-sm
                            hover:bg-rose-400 transition-all">
                            Logout
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="bg-rose-50
                        text-rose-500
                        px-5 py-2.5
                        rounded-2xl font-bold
                        text-sm
                        hover:bg-rose-500 hover:text-white transition-all">
                        Login
                    </a>
                @endif
            </div>
        </div>
    </div>
</header>
