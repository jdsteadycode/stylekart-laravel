@extends('vendor.layouts.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Manage Products</h1>

    <a href="{{ route('vendor.products.create') }}"
        class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-400">
        ➕ Add Product
    </a>
</div>

{{-- filter section --}}
<div class="flex flex-wrap items-center gap-4 mb-6">

    <a href="{{ route('vendor.products.index') }}"
       class="px-4 py-2 rounded-full text-sm font-medium
              {{ request('status') === null ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
        All
    </a>

    <a href="{{ route('vendor.products.index', ['status' => 'active']) }}"
       class="px-4 py-2 rounded-full text-sm font-medium
              {{ request('status') === 'active' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
        Active
    </a>

    <a href="{{ route('vendor.products.index', ['status' => 'inactive']) }}"
       class="px-4 py-2 rounded-full text-sm font-medium
              {{ request('status') === 'inactive' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
        Inactive
    </a>

</div>

<div class="bg-white border border-slate-200 rounded-lg overflow-hidden">

    @if (session('success'))
        <div class="m-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- products table section --}}
    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Base Price</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($products as $product)
                <tr>
                    <td class="px-4 py-3">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-4 py-3 font-medium">
                        <a
                            class="text-black hover:underline"
                            href="{{ route('vendor.products.show', $product) }}"
                        >
                            {{ $product->name }}
                        </a>
                    </td>

                    <td class="px-4 py-3">
                        {{ $product->subCategory?->name ?? 'N/A' }}
                    </td>

                    <td class="px-4 py-3">
                        RS{{ number_format($product->base_price, 2) }}
                    </td>

                    <td class="px-4 py-3">
                        <form action="{{ route('vendor.products.toggle-status', $product) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="px-2 py-1 text-sm rounded-full
                                {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>



                    <td class="px-4 py-3 space-x-3" x-data="{ open: false }">

                        <a href="{{ route('vendor.products.edit', $product) }}"
                           class="text-yellow-600 hover:underline">
                            Edit
                        </a>

                        <x-admin.delete-modal
                            action="{{ route('vendor.products.destroy', $product) }}"
                            title="{{ 'Are you sure?'}}"
                            message="{{ 'To delete ' . $product->name }}"
                        />

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="px-4 py-6 text-center text-slate-500">
                        No products found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<div class="mt-6">
    {{ $products->links() }}
</div>

@endsection
