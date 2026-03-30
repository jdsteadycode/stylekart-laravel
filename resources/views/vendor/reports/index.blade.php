@extends('vendor.layouts.app')

@section('content')
    <script src="{{ asset('js/vendor-reports.js') }}" defer></script>

    {{-- 📢 Clean Error Alerts --}}
    @if ($errors->any() || session('error'))
        <div class="mb-6 p-4 bg-white border-l-4 border-red-500 rounded-2xl shadow-sm flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <div>
                <p class="text-sm font-bold text-slate-800 uppercase tracking-tight">Report Issue</p>
                <p class="text-xs text-slate-500">{{ $errors->first() ?? session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-800 uppercase">Report Center</h2>
        <p class="text-gray-500 text-sm mt-1">Select a category to generate a specific business analysis.</p>
    </div>

    {{-- 📊 Report Selection Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <button onclick="prepareReport('delivered', 'Sales Report')" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:border-indigo-500 hover:shadow-xl transition-all group text-left">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">✅</div>
            <h3 class="text-lg font-bold text-gray-800">Sales Report</h3>
            <p class="text-xs text-gray-400 mt-2 font-medium">Successful deliveries only. Best for revenue tracking.</p>
        </button>

        <button onclick="prepareReport('returns', 'Returns Analysis Report')" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:border-orange-500 hover:shadow-xl transition-all group text-left">
            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">🔄</div>
            <h3 class="text-lg font-bold text-gray-800">Returns Track</h3>
            <p class="text-xs text-gray-400 mt-2 font-medium">Logistics report for all return activity and reasons.</p>
        </button>

        <button onclick="prepareReport('refunds', 'Refunds & Debits Report')" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:border-red-500 hover:shadow-xl transition-all group text-left">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">💵</div>
            <h3 class="text-lg font-bold text-gray-800">Refunds History</h3>
            <p class="text-xs text-gray-400 mt-2 font-medium">Financial audit of money returned to customer wallets.</p>
        </button>
    </div>

    {{-- 🛡️ Modal --}}
    @include('vendor.partials.modal') {{-- Assuming you moved the modal to a partial to keep index clean --}}

    {{-- 📑 Simple Preview Table --}}
    @if(isset($results))
        @if($results->count() > 0)
            {{-- 📑 Data Table (Existing Code) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $stats['type_label'] }}</h3>
                        <p class="text-xs text-gray-500">{{ $stats['date_string'] }} | {{ $stats['total_count'] }} records found</p>
                    </div>
                    <div class="text-right font-black text-indigo-600 text-xl">
                        ₹{{ number_format($stats['total_value'], 2) }}
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-400 uppercase text-[10px] tracking-widest border-b">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Order #</th>
                                <th class="px-6 py-4">Product</th>
                                @if($type !== 'delivered')
                                    <th class="px-6 py-4">Reason</th>
                                @endif
                                <th class="px-6 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($results as $item)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 text-gray-500">{{ $item->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-4 font-bold text-gray-700">#{{ $item->order->order_number }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $item->product->name }}</td>
                                @if($type !== 'delivered')
                                    <td class="px-6 py-4 italic text-slate-500 text-xs">
                                        {{ $item->return_reason ?? 'No reason provided' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-right font-black text-gray-900">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- pagination --}}
                <div class="p-6 border-t border-gray-50 bg-gray-50/30">
                    {{ $results->links() }}
                </div>
            </div>
        @else
            {{-- 🔍 Empty State UI --}}
            <div class="bg-white rounded-3xl p-12 border border-dashed border-gray-200 text-center mb-10">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">🔎</div>
                <h3 class="text-lg font-bold text-slate-800">No Records Found</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-xs mx-auto">
                    We couldn't find any <strong>{{ $type }}</strong> items for the selected period ({{ $stats['date_string'] }}).
                </p>
                <button onclick="prepareReport('{{ $type }}', '{{ $stats['type_label'] }}')" class="mt-6 text-indigo-600 font-bold text-xs uppercase tracking-widest hover:text-indigo-700">
                    Try different filters
                </button>
            </div>
        @endif
    @endif
@endsection
