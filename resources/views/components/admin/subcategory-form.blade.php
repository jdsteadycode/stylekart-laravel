@props([
    'method' => 'POST',
    'action',
    'subcategory' => null,
    'categories'
])

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf

    @if ($method === 'PUT')
        @method('PUT')
    @endif

    {{-- When action is create (i.e., not update) --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Category
        </label>

        {{-- i.e., create not update existing sub category --}}
        @if (!$subcategory)
        <select
            name="category_id"
            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">

            <option value="">-- Select Category --</option>

            @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
        @endif

        @error('category_id')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sub Category Name --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Sub Category Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $subcategory->name ?? ' ') }}"
            placeholder="e.g. T-Shirts"
            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900" />

        @error('name')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="flex gap-3">
        <button
            type="submit"
            class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800">
            {{ $subcategory ? 'Update Subcategory' : 'Create'}}
        </button>

        <a href="{{ url()->previous() }}"
            class="px-4 py-2 text-sm border rounded-md hover:bg-slate-100">
            Cancel
        </a>
    </div>
</form>
