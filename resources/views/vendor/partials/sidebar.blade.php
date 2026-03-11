@php
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

        <a href="{{ route('vendor.products.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.products.*') ? $active : '' }}">
            📦 Products
        </a>

        <a href="{{ route('vendor.orders.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-indigo-800 {{ request()->routeIs('vendor.orders.*') ? $active : '' }}">
            🧾 Orders
        </a>

    </nav>
</aside>
