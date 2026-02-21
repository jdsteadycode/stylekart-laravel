@extends('vendor.layouts.app')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    @if (session('success'))
        <div class="m-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="m-4 text-red-700 bg-red-100 px-4 py-2 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <a
            href="{{ url()->previous() }}"
            class="bg-gray-600 text-white px-4 py-2 text-sm rounded-md hover:bg-gray-500">
            back
        </a>
    </div>

    <!-- Product Details -->
    <div class="bg-white border rounded-lg p-6">

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">
                {{ $product->name }}
            </h1>

            <span class="px-3 py-1 text-xs rounded-full
                {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $product->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <p class="text-sm text-gray-600 mb-4">
            {{ $product->description ?? 'No description provided.' }}
        </p>

        <div class="grid grid-cols-2 gap-6 text-sm">

            <div>
                <strong>Main Category:</strong>
                {{ $product->subCategory->category->name }}
            </div>

            <div>
                <strong>Sub Category:</strong>
                {{ $product->subCategory->name }}
            </div>

            <div>
                <strong>Base Price:</strong>
                ₹ {{ number_format($product->base_price, 2) }}
            </div>

            <div>
                <strong>Created:</strong>
                {{ $product->created_at->format('d M Y') }}
            </div>

        </div>

    </div>

    <!-- Colors Section -->
    <div class="bg-white border rounded-lg p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Colors</h2>

            <a href="{{ route('vendor.products.colors.create', $product) }}"
               class="bg-blue-600 text-white px-4 py-2 text-sm rounded-md hover:bg-blue-500">
                + Add Color
            </a>
        </div>

        @if($product->colors->count())

            <div class="space-y-3">
                @foreach($product->colors as $color)
                    <div class="flex justify-between items-center border rounded-md px-4 py-3"
                        >

                        <div class="flex items-center space-x-3">
                            <!-- Optional small color indicator -->
                            <div class="w-4 h-4 rounded-full border"
                                 style="background-color: {{ $color->name }};">
                            </div>

                            <a
                                href="{{ route('vendor.products.colors.show', [$product, $color]) }}"
                            >
                                <span class="text-sm font-medium capitalize hover:underline">
                                    {{ $color->name }}
                                </span>
                            </a>

                            <!-- Optional: Variant count -->
                            <span class="text-xs text-gray-500">
                                ({{ $product->variants->where('color_id', $color->id)->count() }} variants)
                            </span>
                        </div>

                        <div class="space-x-3 text-sm" x-data="{open: false}">
                            <a href="{{ route('vendor.products.colors.edit', [$product, $color]) }}"
                               class="text-orange-600 hover:underline">
                                Edit
                            </a>

                            <x-admin.delete-modal
                                action="{{ route('vendor.products.colors.destroy', [$product, $color]) }}"
                                message="Delete {{ $color->name }}!"
                                title="Are you sure?"
                            />
                        </div>

                    </div>
                @endforeach
            </div>

        @else
            <p class="text-gray-500 text-sm">
                No colors added yet. Add a color to define how this product appears visually.
            </p>
        @endif

    </div>


    <!-- Variants Section -->
    <div class="bg-white border rounded-lg p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Variants</h2>

            <a href="{{ route('vendor.products.variants.create', $product) }}"
               class="bg-blue-600 text-white px-4 py-2 text-sm rounded-md hover:bg-blue-500">
                + Add Variant
            </a>
        </div>

        @if($product->variants->count())
            <table class="w-full text-sm border">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Size</th>
                        <th class="p-2 border">Color</th>
                        <th class="p-2 border">SKU</th>
                        <th class="p-2 border">Price</th>
                        <th class="p-2 border">Stock</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($product->variants as $variant)
                        <tr>
                            <td class="text-center p-2 border">{{ $variant->size }}</td>
                            <td class="text-center p-2 border">{{ $variant->color->name }}</td>
                            <td class="text-center p-2 border">{{ $variant->sku }}</td>
                            <td class="text-center p-2 border">
                                ₹ {{ number_format($variant->price, 2) }}
                            </td>
                            <td class="text-center p-2 border">{{ $variant->stock }}</td>
                            <td class="text-center p-2 border space-x-2" x-data="{open: false}">
                                <a
                                    href="{{ route('vendor.products.variants.edit', ['product' => $product, 'variant' => $variant]) }}"
                                    class="text-orange-600 hover:underline">Edit</a>

                                <x-admin.delete-modal
                                    action="{{ route('vendor.products.variants.destroy', ['product' => $product, 'variant' => $variant]) }}"
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        @else
            <p class="text-gray-500 text-sm">
                No variants added yet.
            </p>
        @endif

    </div>

    <!-- images section -->



</div>

@endsection
