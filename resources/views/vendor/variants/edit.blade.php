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
            Edit Variant for: {{ $product->name }}
        </h2>

        <form action="{{ route('vendor.products.variants.update', [$product, $variant]) }}"
              method="POST"
              class="space-y-6">

            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Size</label>
                <input type="text" name="size"
                       class="w-full border rounded-md p-2"
                       value="{{ old('size', $variant->size) }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Color</label>
                <!--<input type="text" name="color"
                       class="w-full border rounded-md p-2"
                       value="{{ old('color', $variant->color) }}">-->

                       <select name="color_id" required class="w-full rounded">
                           <option value="">Select Color</option>
                           @foreach($product->colors as $color)
                               <option value="{{ $color->id }}"
                                   {{ old('color', $variant->color_id) == $color->id ? 'selected' : '' }}>
                                   {{ ucfirst($color->name) }}
                               </option>
                           @endforeach
                       </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">SKU</label>
                <input type="text" name="sku"
                       class="w-full border rounded-md p-2"
                       value="{{ old('sku', $variant->sku) }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" step="0.01" name="price"
                       class="w-full border rounded-md p-2"
                       value="{{ old('price', $variant->price) }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input type="number" name="stock"
                       class="w-full border rounded-md p-2"
                       value="{{ old('stock', $variant->stock) }}">
            </div>

            <div>
                <button type="submit"
                        class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-500">
                    Update Variant
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
