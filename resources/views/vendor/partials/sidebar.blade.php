@php
// for current active module..
$active = 'bg-indigo-800';
@endphp

<aside class="w-64 bg-indigo-900 text-indigo-100 min-h-screen">
    <nav class="p-4 space-y-1 text-sm">

        <a href="{{ route('dashboard.vendor') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('dashboard.vendor') ? $active : '' }}">
            🏠 Dashboard
        </a>

        <a href="{{ route('vendor.profile.edit') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.profile.*') ? $active : '' }}">
            🏬 My Shop Profile
        </a>

        {{-- brands --}}
        <a href="{{ route('vendor.brands.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.brands.*') ? $active : '' }}">
            🏷️ Brands
        </a>

        <a href="{{ route('vendor.products.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.products.*') ? $active : '' }}">
            📦 Products
        </a>

        {{-- New: added discounts module link --}}
        <a href="{{ route('vendor.discounts.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.discounts.*') ? $active : '' }}">
            🏷️ Discounts
        </a>

        {{-- orders module link --}}
        <a href="{{ route('vendor.orders.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.orders.*') ? $active : '' }}">
            🧾 Orders
        </a>

        {{-- returns module link --}}
        <a href="{{ route('vendor.return.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.return.*') ? $active : '' }}">
            🔄 Return Orders
        </a>

        {{-- NEW: Wallet / Earnings link --}}
        <a href="{{ route('vendor.wallet.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->route('vendor.wallet.*') ? $active : '' }}">
            💰 Earnings (Wallet)
        </a>

        {{-- Reports module link --}}
        <a href="{{ route('vendor.reports.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.reports.*') ? $active : '' }}">
            📊 Business Reports
        </a>
    </nav>
</aside>
