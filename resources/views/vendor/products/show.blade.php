@extends('vendor.layouts.app')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    <div>
        <a
            href="{{ url()->previous() }}"
            class="bg-gray-600 text-white px-4 py-2 text-sm rounded-md hover:bg-gray-500">
            back
        </a>
    </div>

    <!-- Product Details -->
    <div class="bg-white border rounded-lg p-6">

        @if (session('success'))
            <div class="m-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
                {{ session('success') }}
            </div>
        @endif

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
                            <td class="text-center p-2 border">{{ $variant->color }}</td>
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
    <div class="bg-white border rounded-lg p-6 shadow-sm">

        <h2 class="text-lg font-semibold mb-4">Images by Color</h2>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm">
                 <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Upload Form -->
        <form action="{{ route('vendor.products.color-images.store', $product) }}"
              method="POST"
              enctype="multipart/form-data"
              class="flex flex-col md:flex-row md:items-center gap-2 mb-6">

            @csrf

            <input type="text" name="color" placeholder="Color e.g. red"
                   class="border p-2 rounded flex-1 min-w-[120px]">

            <input type="file" name="images[]" multiple
                   class="border p-2 rounded flex-1 min-w-[200px]">

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500">
                Upload
            </button>

        </form>

        <!-- Display Color Images -->
        <div class="space-y-6">
            @foreach($product->colorImages as $colorImage)
                <div class="border rounded-lg p-4">
                    <!-- Color Header -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium capitalize bg-gray-200 px-2 py-1 rounded">
                            {{ $colorImage->color }}
                        </span>
                        <!-- Optional: total images badge -->
                        <span class="text-xs text-gray-500">{{ $colorImage->getMedia('color_images')->count() }} images</span>
                    </div>


                    <!-- Images Grid -->
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($colorImage->getMedia('color_images') as $media)
                            <div class="flex flex-col items-center">
                                <img src="{{ $media->getUrl() }}"
                                     class="w-full h-32 object-cover rounded mb-2 border border-gray-300">

                                <!-- Buttons -->
                                <div class="flex gap-1">
                                    <!-- Update -->
                                    <form action="{{ route('vendor.products.color-images.update', ['product' => $product, 'media' => $media]) }}"
                                          method="POST" enctype="multipart/form-data"
                                          onsubmit="return confirm('Replace this image?')">
                                        @csrf
                                        @method('PUT')
                                        <input type="file" name="image" class="hidden" onchange="this.form.submit()">
                                        <button type="button" onclick="this.previousElementSibling.click()"
                                                class="bg-gray-400 text-white px-2 py-1 text-xs rounded hover:bg-gray-500">
                                            ✏️
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form action="{{ route('vendor.products.color-images.destroy', ['product' => $product, 'media' => $media]) }}"
                                          method="POST" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-600 text-white px-2 py-1 text-xs rounded hover:bg-red-700">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>


    </div>



</div>

@endsection
