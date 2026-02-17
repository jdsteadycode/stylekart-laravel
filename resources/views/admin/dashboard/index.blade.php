@extends('admin.layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-8 tracking-tight">
    Admin Dashboard
</h2>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

    {{-- Total Categories --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
            📂
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('admin.categories.index') }}"
            >
                Total Categories
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalCategories ?? 0 }}
        </h3>
    </div>

    {{-- Total Subcategories --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
            📑
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('admin.subcategories.index') }}"
            >
                Total Subcategories
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalSubCategories ?? 0 }}
        </h3>
    </div>

    {{-- Approved Vendors --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
            ✅
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('admin.vendors.index', ['status' => 'approved']) }}"
            >
                Approved Vendors
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalApprovedVendors ?? 0 }}
        </h3>
    </div>

    {{-- Pending Vendors --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
            ⏳
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('admin.vendors.index', ['status' => 'pending']) }}"
            >
                Pending Vendors
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalPendingVendors ?? 0 }}
        </h3>
    </div>

    {{-- Rejected Vendors --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100
                hover:shadow-lg hover:-translate-y-1 transition duration-300">

        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mb-3">
            ❌
        </div>

        <p>
            <a
                class="text-sm text-gray-500"
                href="{{ route('admin.vendors.index', ['status' => 'rejected']) }}"
            >
                Rejected Vendors
            </a>
        </p>

        <h3 class="text-3xl font-extrabold mt-2 tracking-tight">
            {{ $totalRejectedVendors ?? 0 }}
        </h3>
    </div>

</div>

{{-- Recent Vendors Table --}}
<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

    <h3 class="text-xl font-semibold mb-6">
        Recent Vendors
    </h3>

    @if(isset($recentVendors) && $recentVendors->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">

                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="py-3 font-semibold text-gray-600">Name</th>
                        <th class="py-3 font-semibold text-gray-600">Email</th>
                        <th class="py-3 font-semibold text-gray-600">Status</th>
                        <th class="py-3 font-semibold text-gray-600">Registered</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($recentVendors as $vendor)
                        <tr class="border-b hover:bg-gray-50 transition duration-200">

                            <td class="py-3">
                                <a
                                    class="text-gray-800 font-medium hover:underline"
                                    href="{{ route('admin.vendors.show', ['vendor' => $vendor]) }}"
                                >
                                    {{ $vendor->name}}
                                </a>
                            </td>

                            <td class="py-3 text-gray-700">
                                {{ $vendor->email }}
                            </td>

                            <td class="py-3 text-gray-700">
                                {{ ucfirst($vendor->vendorProfile->status) }}
                            </td>

                            <td class="py-3">
                                <span class="text-xs text-gray-500">
                                    {{ $vendor->created_at->diffForHumans() }}
                                </span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    @else
        <p class="text-sm text-gray-500">
            No vendors found.
        </p>
    @endif

</div>

@endsection
