@extends('customer.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 lg:p-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900">My Wallet</h1>
        <p class="text-slate-500 font-medium mt-1">Manage your refunds and wallet balance.</p>
    </div>

    {{-- ZONE 1: THE HERO BALANCE CARD (Calm & Secure Theme) --}}
        <div class="bg-gradient-to-br from-teal-500 to-emerald-600 rounded-[2rem] p-8 md:p-10 shadow-xl shadow-emerald-200/50 relative overflow-hidden mb-12">
            {{-- Decorative background elements --}}
            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-48 h-48 bg-teal-900 opacity-10 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:justify-between md:items-end gap-6">
                <div>
                    <p class="text-emerald-50 text-xs font-bold tracking-widest uppercase mb-2 drop-shadow-sm">Available Balance</p>
                    <h2 class="text-5xl md:text-6xl font-black text-white tracking-tight drop-shadow-md">
                        <span class="text-emerald-200 mr-1 opacity-80">₹</span>{{ number_format($wallet->balance, 2) }}
                    </h2>
                </div>

                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 shadow-sm">
                    <div class="h-2 w-2 rounded-full bg-white animate-pulse shadow-[0_0_8px_rgba(255,255,255,0.8)]"></div>
                    <span class="text-white text-xs font-bold tracking-wide">Active & Secure</span>
                </div>
            </div>
        </div>

    {{-- ZONE 2: TRANSACTION HISTORY (THE LEDGER) --}}
    <div>
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Transaction History
        </h3>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            @forelse ($transactions as $transaction)
                <div class="p-6 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors flex items-center justify-between gap-4">

                    {{-- Left Side: Icon & Details --}}
                    <div class="flex items-center gap-4">
                        @if($transaction->type === 'credit')
                            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-inner">
                                <i class="fa-solid fa-arrow-down"></i>
                            </div>
                        @else
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center text-lg shadow-inner">
                                <i class="fa-solid fa-arrow-up"></i>
                            </div>
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
                            <p class="text-lg font-black text-emerald-600">+ ₹{{ number_format($transaction->amount, 2) }}</p>
                        @else
                            <p class="text-lg font-black text-slate-900">- ₹{{ number_format($transaction->amount, 2) }}</p>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <div class="h-24 w-24 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-4xl text-slate-300">
                        <i class="fa-solid fa-receipt border-2 border-slate-200 p-4 rounded-3xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">No transactions yet</h4>
                    <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">When you return items or receive refunds, they will securely appear right here.</p>
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
