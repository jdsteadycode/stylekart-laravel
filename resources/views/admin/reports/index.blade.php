@extends('admin.layouts.app')

@if($errors->any())
    @foreach($errors->all() as $error)
        <p>
            {{ $error }}
        </p>
    @endforeach
@endif

@section('content')
<script src="{{ asset('js/admin-reports.js') }}" defer></script>

<div class="mb-8">
    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Global Audit Center</h2>
    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mt-1">Platform-wide financial and vendor analysis</p>
</div>

{{-- Report Type Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-10">
    @php
        $cards = [
            ['id' => 'delivered', 'label' => 'Sales', 'icon' => '📈', 'color' => 'green'],
            ['id' => 'returns', 'label' => 'Returns', 'icon' => '🔄', 'color' => 'orange'],
            ['id' => 'vendors', 'label' => 'Vendors', 'icon' => '🏆', 'color' => 'purple'],
            ['id' => 'wallets', 'label' => 'Wallets', 'icon' => '💳', 'color' => 'blue'],
            ['id' => 'refunds', 'label' => 'Refunds', 'icon' => '💵', 'color' => 'red'],
        ];
    @endphp
    @foreach($cards as $card)
    <button onclick="prepareReport('{{ $card['id'] }}', '{{ $card['label'] }} Report')" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-{{ $card['color'] }}-500 transition-all group text-left">
        <div class="text-2xl mb-2 group-hover:scale-125 transition">{{ $card['icon'] }}</div>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $card['label'] }}</span>
    </button>
    @endforeach
</div>

@include('admin.partials.modal')

@if(isset($results))
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 bg-slate-50/50 flex justify-between items-center border-b border-slate-100">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ $stats['type_label'] }}</h3>
                {{-- fixed | part --}}
                @if(isset($stats['date_string']) && $stats['date_string'] !== '')
                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $stats['date_string'] }} | {{ $stats['total_count'] }} Found</p>
                @else
                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $stats['total_count'] }} Found</p>
                @endif
            </div>
            <div class="text-xl font-black text-indigo-600">
                {{ $type == 'vendors' ? '' : '₹' . number_format($stats['total_value'], 2) }}
            </div>
        </div>

        @if($type != 'vendors')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-white border-b border-slate-100">
                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Total Records</p>
                    <p class="text-lg font-black text-slate-800">{{ $stats['total_count'] }}</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Total Value</p>
                    <p class="text-lg font-black text-indigo-600">₹{{ number_format($stats['total_value'], 2) }}</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Average Value</p>
                    <p class="text-lg font-black text-green-600">
                        ₹{{ number_format($stats['avg_value'] ?? 0, 2) }}
                    </p>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Max Value</p>
                    <p class="text-lg font-black text-red-500">
                        ₹{{ number_format($stats['max_value'] ?? 0, 2) }}
                    </p>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b">
                    <tr>
                        @if($type == 'wallets')
                            <th class="px-6 py-4">Customer</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Balance</th>
                        @elseif($type == 'vendors')
                            <th class="px-6 py-4">Vendor</th><th class="px-6 py-4">Join Date</th><th class="px-6 py-4 text-right">Delivered Items</th>
                        @else
                            <th class="px-6 py-4">Date</th><th class="px-6 py-4">Vendor</th><th class="px-6 py-4">Order #</th><th class="px-6 py-4 text-right">Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($results as $res)
                    <tr class="hover:bg-slate-50/50 transition">
                        @if($type == 'wallets')
                            <td class="px-6 py-4 font-bold text-slate-700">
                                <a
                                    href="{{ route('admin.reports.wallet.details', array_merge(
                                        ['user' => optional($res->user)->id ?? null],
                                        request()->query()
                                    )) }}"
                                    class="text-indigo-600 hover:underline"
                                >
                                    {{ optional($res->user)->name ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-[10px] font-black text-green-500 uppercase">Active</td>
                            <td class="px-6 py-4 text-right font-black text-black uppercase">{{ $res->balance ?? 'N/A' }}</td>
                        @elseif($type == 'vendors')
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $res->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $res->created_at->format('M Y') }}</td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600">{{ $res->total_sales_count ?? 0 }}</td>
                        {{-- for return, sales, refunds --}}
                        @else
                            <td class="px-6 py-4 text-slate-400">{{ $res->created_at->format('d M') }}</td>
                            <td
                                class="px-6 py-4 font-black text-indigo-500 text-[10px] uppercase">{{ optional($res->vendor)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-700">#{{ optional($res->order)->order_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">₹{{ number_format($res->price * $res->quantity, 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-50">{{ $results->appends(request()->query())->links() }}</div>
    </div>
@endif
@endsection
