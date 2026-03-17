@php
    // get total notifications un-read
    $unreadCount = auth()->user()->unreadNotifications->count();
@endphp

<nav class="bg-white border-b border-slate-200">
    <div class="px-6 py-3 flex items-center justify-between">

        {{-- Left: Brand --}}
        <div class="flex items-center gap-2">
            <span class="text-xl">🛍️</span>
            <span class="text-lg font-semibold tracking-wide">
                StyleKart
            </span>
            <span class="text-sm text-slate-500">
                Delivery
            </span>
        </div>

        {{-- Right: User + Logout --}}
        <div class="flex items-center gap-4 text-sm">

            {{-- deliveries available notification --}}
            <div class="relative">
                <a href="{{ route('delivery.notifications.index') }}" class="text-slate-600 hover:text-indigo-600 transition">
                    <span class="text-lg">🔔</span>
                    @if($unreadCount > 0)
                        <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white animate-pulse">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>


            <span class="text-slate-600">
                👋 {{ auth()->user()->name ?? 'Delivery' }}
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
