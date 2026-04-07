@php
$active = 'bg-slate-800';
@endphp

<aside class="w-64 bg-slate-900 text-slate-100 min-h-screen">
    <nav class="p-4 space-y-1 text-sm">

        <a href="{{ route('dashboard.delivery') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('dashboard.delivery') ? $active : '' }}">
            📦 Orders
        </a>

        {{-- Wallet Link --}}
        <a href="{{ route('delivery.wallet.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->route('delivery.wallet.*') ? $active : '' }}">
            💰 Earnings (Wallet)
        </a>

        <a href="{{ route('delivery.return.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('delivery.return.index') ? $active : '' }}">
            ↩️ Returns
        </a>
    </nav>
</aside>
