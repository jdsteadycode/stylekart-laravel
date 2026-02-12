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
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800">
            🗂️ Categories
        </a>

        <a href="{{ route('admin.subcategories.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800">
            🧩 Sub Categories
        </a>

        <a href="#"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800">
            📦 Stock
        </a>

        <a href="#"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800">
            🏪 Vendors
        </a>

        <a href="#"
            class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-800">
            🚚 Delivery Persons
        </a>

    </nav>
</aside>