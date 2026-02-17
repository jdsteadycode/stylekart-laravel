@props([
    'action',
    'vendor',
    'vendorProfile'
])

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- Vendor Name --}}
    <div>
        <label class="block text-sm font-medium mb-1">Your Name</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $vendor->name ?? '') }}"
            placeholder="e.g. Your good name"
            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"
        />
        @error('name')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Shop Name --}}
    <div>
        <label class="block text-sm font-medium mb-1">Shop Name</label>
        <input
            type="text"
            name="shop_name"
            value="{{ old('shop_name', $vendorProfile->shop_name ?? '') }}"
            placeholder="e.g. Your shop name"
            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"
        />
        @error('shop_name')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Shop Address --}}
    <div>
        <label class="block text-sm font-medium mb-1">Shop Address</label>
        <textarea
            name="shop_address"
            rows="3"
            placeholder="e.g. 123 Main Street, City, State"
            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 resize-none"
        >{{ old('shop_address', $vendorProfile->shop_address ?? '') }}</textarea>
        @error('shop_address')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>


    {{-- Actions --}}
    <div class="flex gap-3">
        <button
            type="submit"
            class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800">
                Save details
        </button>

        <a href="{{ url()->previous() }}"
            class="px-4 py-2 text-sm border rounded-md hover:bg-slate-100">
            Cancel
        </a>
    </div>
</form>
