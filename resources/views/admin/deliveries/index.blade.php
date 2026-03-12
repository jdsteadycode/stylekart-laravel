@extends('admin.layouts.app')

@section('content')

{{-- Success/Error Messages --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
        {{ session('success') }}
    </div>
@elseif($errors->any())
    @foreach($errors->all() as $error)
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ $error }}
        </div>
    @endforeach
@endif

<div class="bg-white rounded-lg shadow-sm p-6">
    {{-- Changed Title to reflect monitoring --}}
    <h1 class="text-2xl font-bold mb-6 text-slate-700">Delivery Monitoring</h1>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 uppercase text-xs font-semibold">
                    <th class="p-4">Order #</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Amount</th>
                    <th class="p-4">Consolidated Status</th>
                    {{-- Changed Column Header --}}
                    <th class="p-4">Assignment Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-4 font-medium text-blue-600">{{ $order->order_number }}</td>
                    <td class="p-4">{{ $order->user->name }}</td>
                    <td class="p-4">₹{{ number_format($order->total_amount, 2) }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">Ready</span>
                    </td>
                    <td class="p-4">
                        {{-- Logic: Form is gone. Show monitoring status instead --}}
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </span>
                            <span class="text-sm font-medium text-amber-700">
                                Waiting for Delivery Partner
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-slate-500 italic">
                        No orders are currently waiting for delivery pickup.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
