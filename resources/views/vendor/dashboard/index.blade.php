@extends('vendor.layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-8 tracking-tight">
    Dashboard
</h2>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

    {{-- Total Products --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mb-3">
            📦
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('vendor.products.index') }}"
            >
                Total Products
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalProducts ?? 0 }}
        </h3>
    </div>

    {{-- Active Products --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mb-3">
            ✅
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('vendor.products.index', ['status' => 'active']) }}"
            >
                Active Products
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalActiveProducts ?? 0 }}
        </h3>
    </div>

    {{-- Inactive Products --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mb-3">
            ❌
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('vendor.products.index', ['status' => 'inactive']) }}"
            >
                In-Active Products
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalInActiveProducts ?? 0 }}
        </h3>
    </div>

</div>


{{-- Recent Products --}}
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

    <h3 class="text-xl font-semibold mb-6">
        Recent Products
    </h3>

    @if(isset($recentProducts) && $recentProducts->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">

                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="py-3 font-semibold text-gray-600">Product</th>
                        <th class="py-3 font-semibold text-gray-600">Variants</th>
                        <th class="py-3 font-semibold text-gray-600">Images</th>
                        <th class="py-3 font-semibold text-gray-600">Updated</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($recentProducts as $product)
                        <tr class="border-b hover:bg-gray-50 transition duration-200">

                            <td class="py-3">
                                <a
                                    class="text-gray-800 font-medium hover:underline"
                                    href="{{ route('vendor.products.show', ['product' => $product]) }}"
                                >
                                    {{ $product->name }}
                                </a>
                            </td>

                            <td class="py-3 text-gray-700">
                                {{ $product->variants->count() ?? 0 }}
                            </td>

                            <td class="py-3 text-gray-700">
                                {{ $product->total_images ?? 0 }}
                            </td>

                            <td class="py-3">
                                <span class="text-xs text-gray-500">
                                    {{ $product->updated_at->diffForHumans() }}
                                </span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    @else
        <p class="text-sm text-gray-500">
            No products added yet.
        </p>
    @endif

</div>

@endsection
