<footer class="bg-white border-t border-rose-50 pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-16">

            <div class="col-span-1 md:col-span-1">
                <a href="{{ route('customer.home') }}" class="flex items-center space-x-2 mb-6">
                    <div class="bg-rose-500 p-2 rounded-xl">
                        <span class="text-white">🛍️</span>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-800">Stylekart</span>
                </a>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">{{ __('footer.description') }}</p>
                {{-- <div class="flex space-x-4">
                    <a href="#" class="h-10 w-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="h-10 w-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="h-10 w-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all"><i class="fa-brands fa-facebook-f"></i></a>
                </div> --}}
            </div>

            <div>
                <h4 class="font-black text-gray-900 mb-6 uppercase text-xs tracking-[0.2em]">{{ __('footer.shop_collection') }}</h4>
                <ul class="space-y-4 text-gray-500 text-sm font-medium">
                    <li><a href="#" class="hover:text-rose-500 transition-colors">{{ __('footer.womens_fashion') }}</a></li>
                    <li><a href="#" class="hover:text-rose-500 transition-colors">{{ __('footer.mens_essentials') }}</a></li>
                    <li><a href="#" class="hover:text-rose-500 transition-colors">{{ __('footer.kids_wear') }}</a></li>
                </ul>
            </div>

            {{-- <div>
                <h4 class="font-black text-gray-900 mb-6 uppercase text-xs tracking-[0.2em]">Our Creators</h4>
                <ul class="space-y-4 text-gray-500 text-sm font-medium">
                    <li><a href="#" class="hover:text-rose-500 transition-colors">Become a Vendor 🏠</a></li>
                    <li><a href="#" class="hover:text-rose-500 transition-colors">Vendor Story 🎨</a></li>
                    <li><a href="#" class="hover:text-rose-500 transition-colors">Sell Globally 🌍</a></li>
                </ul>
            </div> --}}
        </div>

        <div class="pt-8 border-t border-rose-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">
                {{ __('footer.copyright', ['year' => date('Y')]) }}
            </p>
            <div class="flex gap-6">
                <i class="fa-brands fa-cc-visa text-gray-300 text-2xl"></i>
                <i class="fa-brands fa-cc-mastercard text-gray-300 text-2xl"></i>
                <i class="fa-brands fa-cc-apple-pay text-gray-300 text-2xl"></i>
            </div>
        </div>
    </div>
</footer>
