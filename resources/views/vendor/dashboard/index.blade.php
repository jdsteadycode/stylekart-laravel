@extends('vendor.layouts.app')

@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-gray-800">
        Dashboard
    </h2>
    <p class="text-gray-500 text-sm mt-1">Here is your daily store overview and action items.</p>
</div>

{{-- 🚀 Priority Action & Revenue Section --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

    {{-- Orders to Ship (Action Item #1) --}}
    {{-- <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-orange-500 hover:shadow-lg hover:-translate-y-1 transition duration-300">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Orders to Ship</p>
            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-xl">
                📦
            </div>
        </div>
        <h3 class="text-4xl font-black mt-2 tracking-tight text-gray-800">
            {{ $ordersToShip ?? 0 }}
        </h3>
        <a href="{{ route('vendor.orders.index', ['status' => 'processing']) }}" class="text-xs text-orange-600 font-medium hover:underline mt-4 inline-block">
            View processing orders &rarr;
        </a>
    </div> --}}

    {{-- Orders to Ready (Action Item #1) --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-orange-500 hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Orders to Ready</p>
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-xl">
                    📦
                </div>
            </div>
            <h3 class="text-4xl font-black mt-2 tracking-tight text-gray-800">
                {{ $ordersToReady ?? 0 }}
            </h3>
            <a href="{{ route('vendor.orders.index', ['status' => 'pending']) }}" class="text-xs text-orange-600 font-medium hover:underline mt-4 inline-block">
                View pending orders &rarr;
            </a>
        </div>

    {{-- Today Revenue --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition duration-300">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Today's Revenue</p>
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-xl">
                💵
            </div>
        </div>
        <h3 class="text-4xl font-black mt-2 tracking-tight text-gray-800">
            ₹{{ number_format($todayRevenue ?? 0, 2) }}
        </h3>
        <p class="text-xs text-gray-400 mt-4">From paid orders today</p>
    </div>

    {{-- This Month Revenue --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition duration-300">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">This Month</p>
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-xl">
                📆
            </div>
        </div>
        <h3 class="text-4xl font-black mt-2 tracking-tight text-gray-800">
            ₹{{ number_format($thisMonthRevenue ?? 0, 2) }}
        </h3>
        <p class="text-xs text-gray-400 mt-4">Current month total</p>
    </div>

</div>

{{-- 📈 Business Performance Section --}}
<div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-10">
    <h3 class="text-xl font-bold text-gray-800">Business Performance</h3>
    <p class="text-sm text-gray-500 mb-6">Monthly revenue trend for this year</p>

    <div class="h-[300px] w-full">
        {{-- This ID MUST match the one in your JavaScript --}}
        <canvas id="performanceChart"></canvas>
    </div>
</div>

{{-- Donut Chart for order statuses --}}
<div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-10">
    <h3 class="text-xl font-bold text-gray-800 mb-2">Order Health</h3>
    <p class="text-sm text-gray-500 mb-6">Current status of all your orders</p>

    <div class="h-[250px] w-full">
        <canvas id="statusChart"></canvas>
    </div>
</div>


{{-- ⚠️ Actionable Table: Low Stock Alerts --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
        <h3 class="text-xl font-bold tracking-tight flex items-center text-gray-800">
            <span class="mr-2">⚠️</span> Low Stock Alerts
        </h3>
        @if(isset($lowStockVariants) && $lowStockVariants->count() > 0)
            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">
                Action Required
            </span>
        @endif
    </div>

    @if(isset($lowStockVariants) && $lowStockVariants->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="border-b bg-white text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Product</th>
                        <th class="px-6 py-4 font-semibold">Variant (Color/Size)</th>
                        <th class="px-6 py-4 font-semibold text-right">Stock Left</th>
                        <th class="px-6 py-4 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($lowStockVariants as $variant)
                        <tr class="hover:bg-gray-50 transition duration-200">

                            {{-- Product Name (Loaded via Eloquent Relationship) --}}
                            <td class="px-6 py-4 font-medium text-gray-800">
                                <a href="{{ route('vendor.products.show', $variant->product_id) }}" class="hover:underline hover:text-blue-600">
                                    {{ $variant->product->name ?? 'Unknown Product' }}
                                </a>
                            </td>

                            {{-- Variant Details --}}
                            <td class="px-6 py-4 text-gray-600">
                                <span class="inline-flex items-center gap-1.5">
                                    {{-- Optional: Show a tiny color circle if you have hex codes, otherwise just the name --}}
                                    <span class="font-semibold">{{ $variant->color->name ?? 'N/A' }}</span>
                                    <span class="text-gray-400">|</span>
                                    <span>Size: {{ $variant->size }}</span>
                                </span>
                            </td>

                            {{-- Stock Count --}}
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold {{ $variant->stock == 0 ? 'text-red-600' : 'text-orange-500' }}">
                                    {{ $variant->stock }}
                                </span>
                            </td>

                            {{-- Edit Action --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('vendor.products.variants.edit', ['product' => $variant->product_id, 'variant' => $variant->id]) }}"
                                   class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded transition">
                                    Update Stock
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- The "View All" link we discussed --}}
        <div class="p-4 border-t border-gray-50 bg-gray-50/30 text-center">
            <a href="{{ route('vendor.products.index') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium transition">
                Manage all inventory &rarr;
            </a>
        </div>
    @else
        <div class="p-10 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-3xl mb-4">
                ✅
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-1">Inventory Looking Good!</h4>
            <p class="text-sm text-gray-500">None of your product variants are currently running low on stock.</p>
        </div>
    @endif
</div>

{{-- JavaScript --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

new Chart(document.getElementById('performanceChart'), {
        type: 'line',
        data: {
            labels: @json($monthNames),
            datasets: [{
                label: 'Revenue (₹)',
                data: @json($revenueData),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4 // This makes the line curvy
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    const statusData = @json($orderStatusCounts);

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.order_status.toUpperCase()),
            datasets: [{
                data: statusData.map(s => s.count),
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'], // Orange, Blue, Green, Red
                hoverOffset: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' } // Put labels on the side so it looks like a list
            }
        }
    });
</script>
@endsection
