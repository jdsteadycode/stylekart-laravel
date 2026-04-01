@extends('admin.layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-800 uppercase tracking-widest">
                Wallet Details
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                {{ $user->name }} | Balance: ₹{{ number_format($wallet->balance, 2) }}
            </p>
        </div>

        <a href="{{ route('admin.reports.generate', request()->query()) }}"
           class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200">
            ← Back to Reports
        </a>
    </div>

    @if($transactions->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 text-sm font-semibold text-slate-700">
                Transaction History
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">

                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach($transactions as $txn)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-6 py-3 text-slate-600">
                                {{ $txn->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-md text-xs font-semibold
                                    {{ ($txn->type ?? '') === 'credit'
                                        ? 'bg-green-50 text-green-600'
                                        : 'bg-red-50 text-red-600' }}">
                                    {{ ucfirst($txn->type ?? 'N/A') }}
                                </span>
                            </td>

                            <td class="px-6 py-3 text-right font-semibold text-slate-800">
                                ₹{{ number_format($txn->amount ?? 0, 2) }}
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>

        </div>
    @else
    <div class="p-6 bg-yellow-50 border border-yellow-100 rounded-xl text-yellow-600 text-sm font-bold">
        No transactions found for this user.
    </div>
    @endif


@endsection
