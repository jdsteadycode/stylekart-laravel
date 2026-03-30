@extends('vendor.layouts.app')

@section('title', 'Return Requests')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-800">Return Requests</h2>
        <p class="text-gray-500 text-sm mt-1">Manage customer returns and reverse logistics.</p>
    </div>
</div>

{{-- Toasts --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($returnRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="border-b bg-gray-50/50 text-gray-500 uppercase text-[10px] tracking-wider font-black">
                    <tr>
                        <th class="px-6 py-4">Order #</th>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Customer Reason</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    {{-- for each requests --}}
                    @foreach($returnRequests as $req)
                        <tr class="hover:bg-gray-50 transition duration-200">
                            {{-- Order Number --}}
                            <td class="px-6 py-4 font-bold text-gray-800">
                                #{{ $req->order->order_number ?? 'N/A' }}
                            </td>

                            {{-- Product Details --}}
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800">{{ $req->product->name ?? 'Unknown Product' }}</p>
                                <p class="text-xs text-gray-400">Qty: {{ $req->quantity }} | Size: {{ $req->variant->size ?? 'N/A' }}</p>
                            </td>

                            {{-- Reason (Truncated for clean table view) --}}
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $req->return_reason }}">
                                {{ Str::limit($req->return_reason, 40) }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if($req->return_status === 'requested')
                                    <span class="bg-yellow-100 text-yellow-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Requested</span>
                                @elseif($req->return_status === 'approved')
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Approved</span>
                                @elseif($req->return_status === 'rejected')
                                    <span class="bg-red-100 text-red-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Rejected</span>
                                @else
                                    <span class="bg-green-100 text-green-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $req->return_status }}</span>
                                @endif
                            </td>

                            {{-- Action: View Details --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('vendor.return.show', $req->id) }}"
                                   class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition">
                                    Review &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-50">
            {{ $returnRequests->links() }}
        </div>
    @else
        <div class="p-16 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-3xl mb-4">
                📦
            </div>
            <h4 class="text-lg font-bold text-gray-800 mb-1">No Return Requests</h4>
            <p class="text-sm text-gray-500">You currently have no pending or past return requests from customers.</p>
        </div>
    @endif
</div>
@endsection
