<nav class="bg-white border-b border-slate-200">
    <div class="px-6 py-3 flex items-center justify-between">

        {{-- Left: Brand --}}
        <div class="flex items-center gap-2">
            <span class="text-xl">🛍️</span>
            <span class="text-lg font-semibold tracking-wide">
                StyleKart
            </span>
            <span class="text-sm text-slate-500">
                Admin
            </span>
        </div>

        {{-- Right: User + Logout --}}
        <div class="flex items-center gap-4 text-sm">
            <span class="text-slate-600">
                👋 {{ auth()->user()->name ?? 'Admin' }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="text-red-500 hover:text-red-600 transition">
                    🚪 Logout
                </button>
            </form>
        </div>

    </div>
</nav>