@php
    // for checking, if product has on-going active discount or not?
    $discount = $product->getActiveDiscount();

    // get minimum priced variant for this main product
    $lowestVariantPrice = $product->variants->min('price');

    // get discounted price - value.
    $lowestDiscountedPrice = $discount
        ? $product->variants->map(function($variant) use ($discount) {
            return $discount->discount_type === 'percentage'
                ? $variant->price - ($variant->price * $discount->discount_value / 100)
                : $variant->price - $discount->discount_value;
        })->min()
        : null;
@endphp

{{-- little large and attention seeking style v1 --}}
<div class="flex flex-col items-center">
    @if($discount)
        {{-- Greyed out original lowest price --}}
        <span class="text-xs text-gray-400 line-through font-medium">
            ₹{{ number_format($lowestVariantPrice, 0) }}
        </span>

        {{-- Sale Alert: Starting From --}}
        <div class="mt-1 flex items-center gap-1.5">
            <span class="text-s font-medium text-rose-600 tracking-tight">
                🔥 ₹{{ number_format($lowestDiscountedPrice, 0) }} onwards
            </span>
        </div>
    @else
        {{-- Standard Price if no discount --}}
        <span class="text-s font-medium text-neutral-800">
             ₹{{ number_format($lowestVariantPrice, 0) }} onwards
        </span>
    @endif
</div>


{{-- a better simple v2 --}}
<!--
<div class="flex flex-col items-center">
    @if($discount)
        {{-- Original lowest price --}}
        <span class="text-sm text-gray-400 line-through">
            ₹{{ number_format($lowestVariantPrice, 0) }}
        </span>

        {{-- Discounted starting price --}}
        <span class="mt-0.5 text-lg font-semibold text-rose-600">
           🔥 ₹{{ number_format($lowestDiscountedPrice, 0) }} onwards
        </span>
    @else
        {{-- Standard price if no discount --}}
        <span class="text-lg font-semibold text-gray-800">
            ₹{{ number_format($lowestVariantPrice, 0) }} onwards
        </span>
    @endif
</div>
-->
