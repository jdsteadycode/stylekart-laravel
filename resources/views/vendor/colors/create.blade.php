@extends('vendor.layouts.app')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Back Button -->
    <div>
        <a href="{{ route('vendor.products.show', $product) }}"
           class="bg-gray-600 text-white px-4 py-2 text-sm rounded-md hover:bg-gray-500">
            Back
        </a>
    </div>

    <!-- Create Color Card -->
    <div class="bg-white border rounded-lg p-6">

        <h1 class="text-lg font-semibold mb-4">
            Add Color for "{{ $product->name }}"
        </h1>

        <form method="POST" action="{{ route('vendor.products.colors.store', $product) }}" class="space-y-4">
            @csrf

            <!-- Color Name -->
            <div>
                <label for="name" class="block text-sm font-medium mb-1">
                    Color Name
                </label>

                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       placeholder="e.g. Red, Blue, Black"
                       class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">

                <p class="text-xs text-gray-500 mt-1">
                    Color names are automatically normalized (e.g. "BLUE" becomes "blue").
                </p>

                <!--when error -->
                @error('name')
                    <p class="text-red-600 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 text-sm rounded-md hover:bg-blue-500">
                    Save Color
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
