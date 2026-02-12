@props([
    'action',
    'category' => null
])

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @if($category)
        @method('PUT')
    @endif

    {{-- Category Name --}}
    <div>
        <label class="block text-sm font-medium mb-1">Category Name</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            placeholder="e.g. Men"
            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"
        />
        @error('name')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Actions --}}
    <div class="flex gap-3">
        <button
            type="submit"
            class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800">
            {{ $category ? 'Update Category' : 'Create' }}
        </button>

        <a href="{{ url()->previous() }}"
            class="px-4 py-2 text-sm border rounded-md hover:bg-slate-100">
            Cancel
        </a>
    </div>
</form>
