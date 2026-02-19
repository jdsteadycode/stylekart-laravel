@extends('vendor.layouts.app')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('vendor.products.show', $product) }}"
           class="bg-gray-600 text-white px-4 py-2 text-sm rounded-md hover:bg-gray-500">
            Back
        </a>
    </div>

    <div class="bg-white border rounded-lg p-6">

        <h2 class="text-lg font-semibold mb-6">
            Add Variant for: {{ $product->name }}
        </h2>

        <form action="{{ route('vendor.products.variants.store', $product) }}"
              method="POST"
              class="space-y-6">

            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Size</label>
                <input type="text" name="size"
                       class="w-full border rounded-md p-2"
                       value="{{ old('size') }}">
                @error('size')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Color</label>
                <!--<input type="text" name="color"
                       class="w-full border rounded-md p-2"
                       value="{{ old('color') }}">-->

                       <select name="color" required class="w-full rounded">
                           <option value="">Select Color</option>
                           @foreach($product->colors as $color)
                               <option value="{{ $color->name }}"
                                   {{ old('color') == $color->name ? 'selected' : '' }}>
                                   {{ ucfirst($color->name) }}
                               </option>
                           @endforeach
                       </select>

                @error('color')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">SKU</label>
                <input type="text" name="sku"
                       class="w-full border rounded-md p-2"
                       value="{{ old('sku') }}">
                @error('sku')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" step="0.01" name="price"
                       class="w-full border rounded-md p-2"
                       value="{{ old('price') }}">
                @error('price')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input type="number" name="stock"
                       class="w-full border rounded-md p-2"
                       value="{{ old('stock') }}">
                @error('stock')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-500">
                    Save Variant
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
