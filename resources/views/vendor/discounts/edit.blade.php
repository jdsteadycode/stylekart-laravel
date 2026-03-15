{{-- resources/views/vendor/discounts/edit.blade.php --}}
@extends('vendor.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Discount</h2>

        <form action="{{ route('vendor.discounts.update', $discount) }}" method="POST"
              x-data="{ targetType: '{{ old('target_type', $discount->product_id ? 'product' : 'sub_category') }}' }">
            @csrf
            @method('PUT')

            {{-- 1. Name of the Sale --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Discount Name (e.g., Sunday Special)</label>
                <input type="text" name="name" value="{{ old('name', $discount->name) }}"
                    class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- 2. Discount Type --}}
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Discount Type</label>
                    <select name="discount_type" class="w-full border rounded px-3 py-2 @error('discount_type') border-red-500 @enderror">
                        <option value="percentage" {{ old('discount_type', $discount->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed_amount" {{ old('discount_type', $discount->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                    @error('discount_type')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. Discount Value --}}
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $discount->discount_value) }}"
                        class="w-full border rounded px-3 py-2 @error('discount_value') border-red-500 @enderror" required>
                    @error('discount_value')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 4. Target Type --}}
            <div class="mb-6 border-b pb-6">
                <label class="block text-gray-700 font-bold mb-2">What is on sale?</label>
                <select name="target_type" x-model="targetType" class="w-full border rounded px-3 py-2 bg-gray-50 @error('target_type') border-red-500 @enderror">
                    <option value="product">A Specific Product</option>
                    <option value="sub_category">An Entire Sub-Category</option>
                </select>
                @error('target_type')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 5. Dynamic Dropdowns --}}
            <div class="mb-6">
                {{-- Shows ONLY if targetType is 'product' --}}
                <div x-show="targetType === 'product'" x-cloak>
                    <label class="block text-gray-700 font-bold mb-2">Select Product</label>
                    <select name="product_id" class="w-full border rounded px-3 py-2 @error('product_id') border-red-500 @enderror">
                        <option value="">-- Choose a Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Shows ONLY if targetType is 'sub_category' --}}
                <div x-show="targetType === 'sub_category'" x-cloak>
                    <label class="block text-gray-700 font-bold mb-2">Select Sub-Category</label>
                    <select name="sub_category_id" class="w-full border rounded px-3 py-2 @error('sub_category_id') border-red-500 @enderror">
                        <option value="">-- Choose a Sub-Category --</option>
                        @foreach($subCategories as $subCategory)
                            <option value="{{ $subCategory->id }}" {{ old('sub_category_id', $discount->sub_category_id) == $subCategory->id ? 'selected' : '' }}>
                                {{ $subCategory->category->name }} -> {{ $subCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sub_category_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 6. The Timers --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Starts At</label>
                    <input type="datetime-local" name="starts_at"
                        value="{{ old('starts_at', $discount->starts_at->format('Y-m-d\TH:i')) }}"
                        class="w-full border rounded px-3 py-2 @error('starts_at') border-red-500 @enderror" required>
                    @error('starts_at')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Ends At</label>
                    <input type="datetime-local" name="ends_at"
                        value="{{ old('ends_at', $discount->ends_at->format('Y-m-d\TH:i')) }}"
                        class="w-full border rounded px-3 py-2 @error('ends_at') border-red-500 @enderror" required>
                    @error('ends_at')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end mt-8">
                <a href="{{ route('vendor.discounts.index') }}" class="text-gray-600 px-4 py-2 mr-2">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded hover:bg-blue-700">
                    Update Discount
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
