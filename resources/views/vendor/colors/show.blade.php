@extends('vendor.layouts.app')

@section('content')

@if (session('success'))
    <div class="m-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="max-w-5xl mx-auto space-y-6">

    <!-- Back to Product -->
    <div>
        <a href="{{ route('vendor.products.show', $color->product) }}"
           class="bg-gray-600 text-white px-4 py-2 text-sm rounded-md hover:bg-gray-500">
           ← Back to Product
        </a>
    </div>

    <!-- Color Details -->
    <div class="bg-white border rounded-lg p-6">

        <!-- Header: Color Name + Swatch + Variant Count -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-2">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full border"
                     style="background-color: {{ $color->name }};">
                </div>
                <h1 class="text-xl font-semibold capitalize">Color: {{ $color->name }}</h1>
            </div>
            <span class="text-sm text-gray-500">
                {{ $color->product->variants->where('color', $color->name)->count() }} variants
            </span>
        </div>

        <!-- Product Info -->
        <p class="text-sm text-gray-600 mb-4">
            Product: <strong>{{ $color->product->name }}</strong>
        </p>

        <!-- Upload New Images -->
        <form action="{{ route('vendor.colors.images.store', [$product, $color]) }}"
              method="POST"
              enctype="multipart/form-data"
              class="flex flex-col md:flex-row md:items-center gap-2 mb-6 border border-gray-200 p-3 rounded">

            @csrf

            <input type="file" name="images[]" multiple
                   class="border p-2 rounded flex-1 min-w-[200px] cursor-pointer">

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500 mt-2 md:mt-0">
                Upload Images
            </button>
        </form>

        <!-- Display Images Grid -->
        @if($color->getMedia('color_images')->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($color->getMedia('color_images') as $media)
                    <div class="flex flex-col items-center border p-2 rounded group hover:shadow-md transition-shadow duration-200">

                        <!-- Image Preview -->
                        <img src="{{ $media->getUrl() }}"
                             class="w-full h-32 object-cover rounded mb-2 border border-gray-300 hover:scale-105 transition-transform duration-200">

                        <!-- Buttons: Replace / Delete -->
                        <div class="flex gap-1">

                            <!-- Replace Image -->
                            <form action="{{ route('vendor.colors.images.update', ['product'=>$product, 'color'=>$color, 'media'=>$media]) }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  onsubmit="return confirm('Replace this image?')">
                                @csrf
                                @method('PUT')
                                <input type="file" name="image" class="hidden" onchange="this.form.submit()">
                                <button type="button" title="Replace image" onclick="this.previousElementSibling.click()"
                                        class="bg-gray-400 text-white px-2 py-1 text-xs rounded hover:bg-gray-500">
                                    ✏️
                                </button>
                            </form>

                            <!-- Delete Image -->
                            <form action="{{ route('vendor.colors.images.destroy', ['product'=>$product, 'color'=>$color, 'media'=>$media]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this image?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete image"
                                        class="bg-red-600 text-white px-2 py-1 text-xs rounded hover:bg-red-700">
                                    🗑️
                                </button>
                            </form>

                        </div>

                        <!-- Optional: Filename / Timestamp -->
                        <span class="text-xs text-gray-400 mt-1 truncate w-full text-center" title="{{ $media->file_name }}">
                            {{ $media->file_name }}
                        </span>

                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty state -->
            <p class="text-gray-500 text-sm flex items-center gap-2 mt-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H3m9 9V3" />
                </svg>
                No images uploaded for this color yet.
            </p>
        @endif

    </div>

</div>

@endsection
