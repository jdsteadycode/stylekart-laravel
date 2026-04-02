@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 lg:p-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900">Platform Revenue</h1>
        <p class="text-slate-500 font-medium mt-1">Track system commissions and wallet balance.</p>
    </div>

    {{-- ZONE 1: THE HERO BALANCE CARD (High Contrast Admin Theme) --}}
        <div class="bg-slate-900 rounded-[2rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 relative overflow-hidden mb-12">
            {{-- Decorative background elements --}}
            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-blue-500 opacity-10 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:justify-between md:items-end gap-6">
                <div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mb-2">Total Commission Earned</p>
                    <h2 class="text-5xl md:text-6xl font-black text-white tracking-tight">
                        <span class="text-blue-400 mr-1">₹</span>{{ number_format($wallet->balance, 2) }}
                    </h2>
                </div>

                <div class="flex items-center gap-2 bg-slate-800 px-4 py-2 rounded-xl border border-slate-700 shadow-sm">
                    <div class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]"></div>
                    <span class="text-slate-300 text-xs font-bold tracking-wide">System Wallet Active</span>
                </div>
            </div>
        </div>

    {{-- ZONE 2: TRANSACTION HISTORY (THE LEDGER) --}}
    <div>
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Commission History
        </h3>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            @forelse ($transactions as $transaction)
                <div class="p-6 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors flex items-center justify-between gap-4">

                    {{-- Left Side: Icon & Details --}}
                    <div class="flex items-center gap-4">
                        @if($transaction->type === 'credit')
                            <div class="text-2xl" title="Credit">💰</div>
                        @else
                            <div class="text-2xl" title="Debit">💸</div>
                        @endif

                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $transaction->description }}</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">
                                {{ $transaction->created_at->format('d M Y, h:i A') }}
                                <span class="mx-1">•</span>
                                <span class="uppercase tracking-wider text-[10px]">TXN-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Right Side: Amount --}}
                    <div class="text-right">
                        @if($transaction->type === 'credit')
                            <p class="text-lg font-black text-indigo-600">+ ₹{{ number_format($transaction->amount, 2) }}</p>
                        @else
                            <p class="text-lg font-black text-slate-900">- ₹{{ number_format($transaction->amount, 2) }}</p>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <div class="h-24 w-24 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-4xl text-slate-300">
                        <i class="fa-solid fa-chart-line border-2 border-slate-200 p-4 rounded-3xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">No commissions yet</h4>
                    <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">When orders are successfully delivered, your 10% platform commission will appear here.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="mt-8">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
