@extends('vendor.layouts.app')

@section('title', 'Review Return Request')

@section('content')
<div class="mb-6">
    <a href="{{ route('vendor.return.index') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-800 transition">
        &larr; Back to Returns
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Left Column: Details --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Customer Reason Card --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Customer's Stated Reason</h3>
            <p class="text-lg font-medium text-gray-800 leading-relaxed bg-gray-50 p-6 rounded-xl border border-gray-100">
                {{ $item->return_reason }}
            </p>
            <p class="text-xs text-gray-400 mt-4 font-medium">
                Requested on {{ $item->updated_at->format('d M, Y \a\t h:i A') }}
            </p>
        </div>

        {{-- Product Information Card --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Item Information</h3>
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 text-3xl">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div>
                    {{-- <a
                        href="{{ route('vendor.products.variants.edit', [
                        'product' => $item->product,
                        'variant' => $item->variant]) }}"
                        class="text-xl font-bold text-gray-900 hover:underline">
                            {{ $item->product->name ?? 'Unknown Product' }}
                    </a> --}}
                    <p
                        class="text-xl font-bold text-gray-900">
                            {{ $item->product->name ?? 'Unknown Product' }}
                    </p>
                    <p class="text-sm text-gray-500 font-medium mt-1">
                        Variant: {{ $item->variant->color->name ?? 'N/A' }} | Size: {{ $item->variant->size ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500 font-medium mt-1">
                        Quantity: <span class="font-bold text-gray-800">{{ $item->quantity }}</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Right Column: Action Panel --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 sticky top-6">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Return Decision</h3>

            <div class="mb-8">
                <p class="text-sm text-gray-500 font-medium mb-1">Refund Amount at Stake</p>
                <p class="text-3xl font-black text-gray-900">₹ {{ number_format($item->price * $item->quantity, 2) }}</p>
            </div>

            @if($item->return_status === 'requested')
                {{-- APPROVE FORM --}}
                <form action="{{ route('vendor.return.approve', $item) }}" method="POST" class="mb-3" onsubmit="return confirm('Are you sure you want to approve this return? A delivery partner will be notified.');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full py-4 bg-green-500 text-white rounded-xl font-bold hover:bg-green-600 transition shadow-lg shadow-green-100 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-check"></i> Approve Return
                    </button>
                </form>

                {{-- REJECT FORM --}}
                <form action="{{ route('vendor.return.reject', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this return?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full py-4 bg-white border-2 border-red-100 text-red-500 rounded-xl font-bold hover:bg-red-50 transition flex justify-center items-center gap-2">
                        <i class="fa-solid fa-xmark"></i> Reject Request
                    </button>
                </form>

                <p class="text-[10px] text-gray-400 mt-4 text-center leading-relaxed">
                    By approving, you authorize the reverse pickup. The refund will only be processed after you receive the item.
                </p>

            {{-- when request is approved! --}}
            @elseif($item->return_status === 'approved')
                <div class="p-6 bg-blue-50 rounded-xl text-center border border-blue-100">
                    <div class="w-12 h-12 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                        {{-- <i class="fa-solid fa-truck-fast"></i> --}}
                        <span>☑️</span>
                    </div>
                    <h4 class="font-bold text-blue-800 mb-1">Return Approved</h4>
                    <p class="text-xs text-blue-600">Waiting for delivery partner to pick up the item from the customer.</p>
                </div>

            {{-- when request is rejected --}}
            @else
                {{-- Fix: changed the background, border and text color --}}
                <div class="p-6 bg-rose-50 rounded-xl text-center border border-rose-100">
                    <h4 class="font-bold text-rose-600 uppercase tracking-widest text-xs">Status: {{ ucfirst($item->return_status) }}</h4>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
