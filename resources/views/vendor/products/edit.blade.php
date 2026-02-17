@extends('vendor.layouts.app')

@section('content')

<div class="max-w-3xl mx-auto bg-white border rounded-lg p-6">

    <h1 class="text-xl font-semibold mb-6">Edit Product</h1>

    <form action="{{ route('vendor.products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm mb-1">Product Name</label>
            <input type="text" name="name"
                class="w-full border rounded-md px-3 py-2 text-sm"
                value="{{ old('name', $product->name) }}">

                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Description</label>
            <textarea name="description"
                class="w-full border rounded-md px-3 py-2 text-sm"
                rows="4">{{ old('description', $product->description) }}</textarea>

            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div class="mb-4">
            <label class="block text-sm mb-1">Category</label>
            <select name="category_id" id="categorySelect"
                class="w-full border rounded-md px-3 py-2 text-sm">

                <option value="">Select Category</option>

                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ $category->id == $product->subCategory->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            @error('category_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div class="mb-4">
            <label class="block text-sm mb-1">Sub Category</label>
            <select name="sub_category_id"
                id="subCategorySelect"
                class="w-full border rounded-md px-3 py-2 text-sm">
            </select>

            @error('sub_category_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div class="mb-6">
            <label class="block text-sm mb-1">Base Price</label>
            <input type="number" step="0.01" name="base_price"
                class="w-full border rounded-md px-3 py-2 text-sm"
                value="{{ old('base_price', $product->base_price) }}">

                    @error('base_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-500">
                Update Product
            </button>
        </div>

    </form>
</div>

<script>
    const categories = @json($categories);
    const currentSubCategoryId = {{ $product->sub_category_id }};

    const categorySelect = document.getElementById('categorySelect');
    const subCategorySelect = document.getElementById('subCategorySelect');

    function populateSubCategories(categoryId) {

        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

        if (!categoryId) return;

        const selectedCategory = categories.find(cat => cat.id == categoryId);

        selectedCategory.sub_categories.forEach(sub => {

            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;

            if (sub.id == currentSubCategoryId) {
                option.selected = true;
            }

            subCategorySelect.appendChild(option);
        });
    }

    categorySelect.addEventListener('change', function () {
        populateSubCategories(this.value);
    });

    // Trigger on page load
    populateSubCategories(categorySelect.value);
</script>
@endsection
