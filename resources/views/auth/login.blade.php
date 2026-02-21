<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Welcome Back ✨</h2>
        <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.3em]">Sign in to your style account</p>
    </div>

    <x-auth-session-status class="mb-6 p-4 bg-green-50 text-green-600 rounded-2xl text-xs font-bold" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Email Address</label>
            <x-text-input id="email"
                class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
        </div>

        <div>
            <div class="flex justify-between items-center ml-1 mb-2">
                <label for="password" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[9px] font-black text-rose-400 uppercase tracking-widest hover:text-rose-600" href="{{ route('password.request') }}">
                        Forgot?
                    </a>
                @endif
            </div>

            <x-text-input id="password"
                class="block w-full bg-rose-50/30 border-rose-50 rounded-2xl px-5 py-4 text-sm focus:border-rose-300 focus:ring-rose-200 transition-all"
                type="password"
                name="password"
                required autocomplete="current-password"
                placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[10px] font-bold text-rose-500 uppercase tracking-tight" />
        </div>

        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded-lg border-rose-100 text-rose-500 shadow-sm focus:ring-rose-300 transition-all" name="remember">
                <span class="ms-2 text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-gray-600 transition-colors">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button class="w-full bg-rose-500 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-rose-100 hover:bg-rose-600 active:scale-95 transition-all">
                {{ __('Log in') }}
            </button>
        </div>

        <p class="text-center text-[10px] font-black text-gray-300 uppercase tracking-widest mt-8">
            New to Stylekart?
            <a href="{{ route('register') }}" class="text-rose-400 hover:text-rose-600 underline underline-offset-4 ml-1">Create Account</a>
        </p>
    </form>
</x-guest-layout>
