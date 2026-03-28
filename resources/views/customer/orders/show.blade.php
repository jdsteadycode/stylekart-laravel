@extends('customer.layouts.app')

@section('title', 'Order Details')

@section('content')

@php
    $statuses = [
        'pending' => 'Placed',
        'processing' => 'Packing',
        'shipped' => 'Shipped',
        'out_for_delivery' => 'On Way',
        'delivered' => 'Delivered'
    ];
@endphp

<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <a href="{{ route('customer.profile') }}" class="text-xs font-black text-rose-400 uppercase tracking-widest flex items-center gap-2 hover:text-rose-600 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Profile
            </a>
        </div>

        {{-- toasts --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200
                        text-green-700 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200
                        text-rose-600 rounded-xl text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <p class="text-orange-500">{{ $error }}</p>
            @endforeach
        @endif

        <div class="bg-white rounded-3xl border border-rose-50 shadow-sm overflow-hidden">

            <div class="p-8 border-b border-rose-50 bg-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.2em] mb-1">Receipt & Status</p>
                        <h1 class="text-2xl font-black text-gray-900">Order # {{ $order->order_number ?? 'N/A' }}</h1>
                        <p class="text-sm text-gray-500 font-medium">Placed on {{ $order->created_at->format('d M, Y \a\t h:i A') ?? 'N/A' }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-2xl font-black text-gray-900">₹ {{ number_format($order->total_amount, 2) ?? 'N/A' }}</p>
                        <span class="inline-block px-4 py-1.5 bg-rose-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest mt-1 shadow-lg shadow-rose-100">
                            {{ $statuses[$order->customer_status] }}
                        </span>
                    </div>
                </div>
                {{-- payment method status section --}}
                <div class="mt-3 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 text-xs">
                            @if(strtolower($order->payment_mode) === 'cod')
                                <i class="fa-solid fa-money-bill-wave"></i>
                            @else
                                <i class="fa-solid fa-credit-card"></i>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">
                            {{ str_replace('_', ' ', $order->payment_mode) }}

                            @if(strtolower($order->payment_status) === 'paid')
                                <span class="ml-2 text-[9px] text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-md font-black tracking-widest">PAID</span>
                            @else
                                <span class="ml-2 text-[9px] text-yellow-600 bg-yellow-50 border border-yellow-200 px-2 py-0.5 rounded-md font-black tracking-widest">PENDING</span>
                            @endif
                        </p>
                </div>
            </div>

            <div class="p-8 bg-rose-50/10">
                {{-- Order Progress / Cancelled UI --}}
                @if($order->order_status === 'cancelled')

                    {{-- Cancelled UI --}}
                    <div class="p-10 bg-rose-50/20 border-t border-b border-rose-100 text-center">

                        <div class="inline-flex items-center justify-center w-16 h-16
                                    rounded-full bg-rose-100 text-rose-500
                                    shadow-lg shadow-rose-100 mb-6">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </div>

                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-rose-500 mb-2">
                            Order Cancelled
                        </h3>

                        <p class="text-xs text-gray-500 font-medium max-w-sm mx-auto leading-relaxed">
                            This order has been successfully cancelled.
                            If payment was completed, the refund will be processed
                            according to our refund policy.
                        </p>

                    </div>

                @else

                    {{-- Normal Timeline UI --}}
                    {{-- <div class="p-8 bg-rose-50/10">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8 text-center">
                            Order Progress
                        </h3>

                        <div class="flex items-center justify-between relative max-w-2xl mx-auto">
                            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-rose-100 -translate-y-1/2"></div>

                            @php
                                $steps = ['pending', 'processing', 'shipped', 'delivered'];

                                $statusColors = [
                                    'pending' => 'bg-yellow-400 text-white shadow-yellow-200',
                                    'processing' => 'bg-blue-500 text-white shadow-blue-200',
                                    'shipped' => 'bg-purple-500 text-white shadow-purple-200',
                                    'delivered' => 'bg-green-500 text-white shadow-green-200',
                                ];

                                $currentStatus = $order->order_status;
                                $currentIndex = array_search($currentStatus, $steps);
                                $currentIndex = $currentIndex !== false ? $currentIndex : -1;
                            @endphp

                            @foreach($steps as $index => $step)
                                @php $isActive = $index <= $currentIndex; @endphp
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs
                                        {{ $isActive ? $statusColors[$step] : 'bg-white border-2 border-rose-100 text-rose-200' }}">
                                        <i class="fa-solid fa-circle"></i>
                                    </div>

                                    <p class="absolute -bottom-6 text-[9px] font-black uppercase tracking-tighter whitespace-nowrap
                                        {{ $isActive ? 'text-gray-900' : 'text-gray-300' }}">
                                        {{ ucfirst($step) }}
                                    </p>
                                </div>
                            @endforeach

                        </div>
                    </div> --}}

                    {{-- Normal Timeline UI --}}
                    <div class="p-10 bg-white rounded-3xl border border-rose-50 shadow-sm mt-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-10 text-center">
                            Track Your Package 🚚
                        </h3>

                        @php
                            // Steps with Labels and Emojis
                            $steps = [
                                'pending'          => ['label' => 'Placed',    'emoji' => '📝'],
                                'processing'       => ['label' => 'Packing',   'emoji' => '📦'],
                                'shipped'          => ['label' => 'Shipped',   'emoji' => '🚛'],
                                'out_for_delivery' => ['label' => 'On Way',    'emoji' => '🛵'],
                                'delivered'        => ['label' => 'Delivered', 'emoji' => '🎁']
                            ];

                            $stepKeys = array_keys($steps);

                            // Aapne accessor vapriye chhiye ($order->customer_status)
                            $currentStatus = $order->customer_status;
                            $currentIndex = array_search($currentStatus, $stepKeys);
                            $currentIndex = ($currentIndex !== false) ? $currentIndex : 0;
                        @endphp

                        <div class="flex items-center justify-between relative max-w-2xl mx-auto px-2">

                            {{-- Progress Line Background --}}
                            <div class="absolute top-[20px] left-0 w-full h-1.5 bg-gray-100 rounded-full"></div>

                            {{-- Active Progress Line (Blue Color) --}}
                            @php
                                $progressWidth = ($currentIndex / (count($steps) - 1)) * 100;
                            @endphp
                            <div class="absolute top-[20px] left-0 h-1.5 bg-teal-500 rounded-full transition-all duration-1000 ease-in-out"
                                 style="width: {{ $progressWidth }}%"></div>

                            @foreach($steps as $key => $data)
                                @php
                                    $isActive = (array_search($key, $stepKeys) <= $currentIndex);
                                    $isCurrent = ($key === $currentStatus);
                                @endphp

                                <div class="relative z-10 flex flex-col items-center">
                                    {{-- Circle with Emoji --}}
                                    <div class="w-10 h-10 rounded-full border-4 flex items-center justify-center text-sm transition-all duration-500
                                        {{ $isCurrent ? 'bg-teal-600 border-teal-200 scale-125 shadow-xl shadow-blue-100' : '' }}
                                        {{ $isActive && !$isCurrent ? 'bg-teal-500 border-white shadow-md' : '' }}
                                        {{ !$isActive ? 'bg-white border-gray-100 grayscale' : '' }}">
                                        {{ $data['emoji'] }}
                                    </div>

                                    {{-- Labels --}}
                                    <p class="absolute -bottom-8 text-[10px] font-black uppercase tracking-widest whitespace-nowrap
                                        {{ $isActive ? 'text-teal-600' : 'text-gray-300' }}">
                                        {{ $data['label'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <div class="p-8 border-t border-rose-50 mt-4">
                <h3 class="font-black text-gray-900 uppercase text-xs tracking-widest mb-6">Items Ordered ({{ $order->items->count() }})</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div x-data="{ open: false }" class="flex items-center gap-6 p-5 rounded-2xl border border-rose-50 hover:bg-rose-50/10 transition-colors">
                        <div class="w-16 h-16 bg-rose-50 rounded-xl flex items-center justify-center text-rose-200 text-xl">
                            <i class="fa-solid fa-shirt"></i>
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-gray-800">{{ $item->product->name ?? 'N/A' }}</h4>
                            <p class="text-xs text-gray-400 font-medium">Qty: {{ $item->quantity ?? 'N/A' }} | Size: {{ $item->variant->size }}</p>

                            @if(
                                in_array($item->order_status, ['pending', 'processing'])
                                && $order->order_status !== 'cancelled'
                            )
                            <button @click="open = true" class="text-xs font-bold text-rose-500 hover:underline">
                                Cancel Item
                            </button>

                            {{-- Modal --}}
                            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                                <div @click.away="open = false" class="bg-white rounded-xl p-6 w-80">
                                    <h3 class="font-bold text-gray-900 text-sm mb-4">Cancel Item?</h3>
                                    <form action="{{ route('customer.order-item.cancel', ['orderNumber' => $order->order_number, 'item' => $item]) }}" method="POST">
                                        @csrf
                                        <label class="block text-xs font-medium text-gray-600 mb-2">
                                            Reason (optional)
                                        </label>
                                        <input type="text" name="cancel_reason" placeholder="Reason..." class="w-full border border-gray-200 rounded-lg p-2 mb-4 text-xs">

                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="open = false" class="px-4 py-2 text-gray-500 text-xs font-bold rounded-lg hover:bg-gray-100">Close</button>
                                            <button type="submit" class="px-4 py-2 bg-rose-500 text-white text-xs font-bold rounded-lg hover:bg-rose-600">Confirm</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- New: Returning an delivered item ordered! -->
                            @elseif($item->order_status === 'delivered' && $item->return_status === null)

                                <div x-data="{ open: false, reasonType: '' }">

                                    {{-- The Trigger Button (Now inside the bubble, so it can see 'open') --}}
                                    <button
                                        @click="open = true"
                                        class="text-xs font-bold text-blue-500 hover:text-blue-700 hover:underline transition-all">
                                        Return Item
                                    </button>

                                    {{-- The Return Modal --}}
                                    <div x-cloak
                                         x-show="open"
                                         class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">

                                        <div @click.away="open = false" class="bg-white rounded-xl p-6 w-80 shadow-2xl transform transition-all">

                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center">
                                                    <i class="fa-solid fa-box-open"></i>
                                                </div>
                                                <h3 class="font-black text-gray-900 text-sm">Return Item?</h3>
                                            </div>

                                            <p class="text-[10px] text-gray-500 mb-4 leading-relaxed">
                                                Please tell us why you are returning this item so we can notify the vendor.
                                            </p>

                                            <form action="{{ route('customer.order-items.return', $item) }}" method="POST">
                                                @csrf
                                                <label class="block text-xs font-bold text-gray-700 mb-2">
                                                    Reason for Return <span class="text-rose-500">*</span>
                                                </label>

                                                {{-- 1. The Dropdown Select --}}
                                                <select x-model="reasonType"
                                                        :name="reasonType === 'Other' ? '' : 'reason'"
                                                        required
                                                        class="w-full border border-gray-200 rounded-lg p-3 mb-4 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all cursor-pointer">
                                                    <option value="" disabled selected>Select a reason...</option>
                                                    <option value="Wrong size or fit">Wrong size or fit</option>
                                                    <option value="Damaged or defective product">Damaged or defective product</option>
                                                    <option value="Item not as described">Item not as described</option>
                                                    <option value="Other">Other (Please specify)</option>
                                                </select>

                                                {{-- 2. The Custom Textarea (Only shows if 'Other' is selected) --}}
                                                <textarea x-show="reasonType === 'Other'"
                                                          :name="reasonType === 'Other' ? 'reason' : ''"
                                                          :required="reasonType === 'Other'"
                                                          rows="3"
                                                          placeholder="Please type your specific reason..."
                                                          class="w-full border border-gray-200 rounded-lg p-3 mb-4 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none transition-all"></textarea>

                                                {{-- Action buttons--}}
                                                <div class="flex justify-end gap-2 mt-2">
                                                    {{-- btn: keep the existing delivered item --}}
                                                    <button type="button" @click="open = false" class="px-4 py-2 text-gray-500 text-xs font-bold rounded-lg hover:bg-gray-100 transition-colors">
                                                        Keep Item
                                                    </button>

                                                    {{-- btn: submit for return request --}}
                                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-xs font-bold rounded-lg hover:bg-blue-600 transition-colors">
                                                        Submit Return
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- show status when delivered item is requested for return! --}}
                            @if($item->return_status === 'requested')
                                <span class="text-xs text-yellow-500 font-bold">
                                    Return Requested
                                </span>
                            @endif

                            {{-- show status when item to be returned is approved! --}}
                            @if($item->return_status === 'approved')
                                <span class="text-xs text-teal-500 font-bold">
                                    Return Approved
                                </span>
                            @endif

                            {{-- show status when item return is rejected! --}}
                            @if($item->return_status === 'rejected')
                                <span class="text-xs text-red-500 font-bold">
                                    Return Rejected
                                </span>
                            @endif
                        </div>
                        @if($item->order_status === 'cancelled')
                            <div class="mt-3 p-3 bg-red-50 rounded-xl text-xs text-red-600">
                                <strong>Cancelled by Vendor</strong><br>
                                Reason: {{ $item->cancel_reason ?? 'No reason provided.' }}
                            </div>
                        @endif
                        <div class="text-right">
                            <p class="font-black text-gray-900 italic">₹ {{ $item->price ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            <div class="p-8 bg-gray-50/50 border-t border-rose-50 flex flex-col md:flex-row justify-between items-center gap-6">
                {{-- <div>
                    <p class="text-xs text-gray-400 font-medium">Need help with this order?</p>
                    <button class="text-xs font-bold text-rose-500 hover:underline">Contact Vendor Support</button>
                </div> --}}

                <div class="flex items-center gap-4">
                    @if(in_array($order->order_status, ['pending', 'processing']))
                    <form action="{{ route('customer.orders.cancel', $order->order_number) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to cancel this entire order?');">

                            @csrf

                            <button type="submit"
                                class="px-6 py-3 bg-white border border-rose-100
                                       text-gray-400 rounded-2xl font-bold text-[10px]
                                       uppercase tracking-widest
                                       hover:bg-rose-50 hover:text-rose-500 transition-all">
                                Cancel Full Order
                            </button>

                        </form>
                    @endif

                    <a href="{{ route('customer.orders.invoice', $order->id) }}"
                       target="_blank"
                       class="px-4 py-2 bg-rose-500 text-white text-xs font-bold rounded-xl hover:bg-rose-600 transition-all">
                       View / Print Invoice
                    </a>


                    <p class="text-[9px] text-gray-300 italic max-w-[120px] leading-tight">
                        Cancellations are only available during 'Processing'.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
