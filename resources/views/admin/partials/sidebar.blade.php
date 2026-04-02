@php
$active = 'bg-slate-800';
@endphp

<aside class="w-64 bg-slate-900 text-slate-100 min-h-screen">
    <nav class="p-4 space-y-1 text-sm">

        <a href="{{ route('dashboard.admin') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('dashboard.admin') ? $active : '' }}">
            📊 Dashboard
        </a>

        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.categories.index') ? $active : ''}}">
            🗂️ Categories
        </a>

        <a href="{{ route('admin.subcategories.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.subcategories.index') ? $active : ''}}">
            🧩 Sub Categories
        </a>

        {{-- <a href="#"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800">
            📦 Stock
        </a> --}}

        <a href="{{ route('admin.vendors.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.vendors.index') ? $active : ''}} ">
            🏪 Vendors
        </a>

        <a href="{{ route('admin.deliveries.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.deliveries.index') ? $active : ''}}">
            🚚 Deliveries
        </a>

        {{-- new: added wallet link --}}
        <a href="{{ route('admin.wallet.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.wallet.index') ? $active : ''}}">
            💰 Wallet
        </a>

        {{-- fixed: business reports emoji --}}
        <a href="{{ route('admin.reports.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.reports.index') ? $active : ''}}">
            📄 Business Reports
        </a>
    </nav>
</aside>
