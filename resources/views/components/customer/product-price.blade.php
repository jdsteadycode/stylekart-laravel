@php
    $discount = $product->getActiveDiscount();
@endphp

<div class="flex flex-col items-center">
    @if($discount)
        {{-- Greyed out original price --}}
        <span class="text-sm text-gray-400 line-through font-medium">₹{{ number_format($product->base_price, 0) }}</span>

        {{-- High-energy Sale Alert (No math, just the deal) --}}
        <div class="mt-1 flex items-center gap-1.5">
            <span class="text-xl font-black text-rose-600 tracking-tight">
                {{ $discount->discount_type === 'percentage' ? round($discount->discount_value).'% OFF' : '₹'.round($discount->discount_value).' OFF' }}
            </span>
        </div>
    @else
        {{-- Standard Price if no discount --}}
        <span class="text-xl font-black text-gray-900">₹{{ number_format($product->base_price, 0) }}</span>
    @endif
</div>
