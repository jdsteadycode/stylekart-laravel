<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Join Stylekart ✨</h2>
        <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.3em]">Start your fashion journey today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Full Name</label>
            <x-text-input id="name" class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
        </div>

        <div>
            <label for="email" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Email Address</label>
            <x-text-input id="email" class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="hello@stylekart.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="password" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Password</label>
                <x-text-input id="password" class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all"
                    type="password"
                    name="password"
                    required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
            </div>

            <div>
                <label for="password_confirmation" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Confirm Password</label>
                <x-text-input id="password_confirmation" class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all"
                    type="password"
                    name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
            </div>
        </div>

        <div>
            <label for="role" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Register as</label>
            <select id="role" name="role" class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all text-gray-500">
                <option value="" default>Select role?</option>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer (I want to shop)</option>
                <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor (I want to sell)</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
        </div>

        <div class="pt-6 space-y-4">
            <button class="w-full bg-rose-500 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-rose-100 hover:bg-rose-600 active:scale-95 transition-all">
                {{ __('Register') }}
            </button>

            <div class="flex items-center justify-center">
                <a class="text-[10px] font-black text-gray-300 uppercase tracking-widest hover:text-rose-500 transition-colors underline underline-offset-4" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
