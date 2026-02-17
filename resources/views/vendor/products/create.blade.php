@extends('vendor.layouts.app')

@section('content')

<div class="max-w-3xl mx-auto bg-white border rounded-lg p-6">

    <h1 class="text-xl font-semibold mb-6">Add Product</h1>

    <form action="{{ route('vendor.products.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm mb-1">Product Name</label>
            <input type="text" name="name"
                class="w-full border rounded-md px-3 py-2 text-sm"
                value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Description</label>
            <textarea name="description"
                class="w-full border rounded-md px-3 py-2 text-sm"
                rows="4">{{ old('description') }}</textarea>

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
                    <option
                        value="{{ $category->id }}
                        ">
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

                <option value="">Select Sub Category</option>

            </select>

            @error('sub_category_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm mb-1">Base Price</label>
            <input type="number" step="0.01" name="base_price"
                class="w-full border rounded-md px-3 py-2 text-sm"
                value="{{ old('base_price') }}">
            @error('base_price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-500">
                Save Product
            </button>
        </div>

    </form>

</div>

<script>

    // get cateogies as array from json
    const categories = @json($categories);
    // check log
    // console.log(categories);

    // grab the elements
    const categorySelect = document.querySelector('#categorySelect');
    const subCategorySelect = document.querySelector('#subCategorySelect');

    // when main category changes (selected)
    categorySelect.addEventListener('change', function () {

        // get current main category id
        const selectedCategoryId = this.value;

        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

        // when id is null do nothing..
        if (!selectedCategoryId) return;

        // get current category
        const selectedCategory = categories.find(cat => cat.id == selectedCategoryId);

        // from it's subcategories
        selectedCategory.sub_categories.forEach(sub => {

            // make option element
            const option = document.createElement('option');

            // set data
            option.value = sub.id;
            option.textContent = sub.name;

            // finally append the option element inside select element.
            subCategorySelect.appendChild(option);
        });
    });
</script>

@endsection
