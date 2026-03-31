@extends('admin.layouts.app')
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
                <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $stats['date_string'] }} | {{ $stats['total_count'] }} Found</p>
            </div>
            <div class="text-xl font-black text-indigo-600">
                {{ $type == 'vendors' ? '' : '₹' . number_format($stats['total_value'], 2) }}
            </div>
        </div>

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
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $res->user->name }}</td>
                            <td class="px-6 py-4 text-[10px] font-black text-green-500 uppercase">Active</td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">₹{{ number_format($res->balance, 2) }}</td>
                        @elseif($type == 'vendors')
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $res->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $res->created_at->format('M Y') }}</td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600">{{ $res->total_sales_count ?? 0 }}</td>
                        @else
                            <td class="px-6 py-4 text-slate-400">{{ $res->created_at->format('d M') }}</td>
                            <td class="px-6 py-4 font-black text-indigo-500 text-[10px] uppercase">{{ $res->vendor->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-700">#{{ $res->order->order_number }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">₹{{ number_format($res->price * $res->quantity, 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-50">{{ $results->links() }}</div>
    </div>
@endif
@endsection
