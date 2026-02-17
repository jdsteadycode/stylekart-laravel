@extends('admin.layouts.app')

@section('content')

<div class="mb-6">
    <div class="mb-4">
        <a
            href="{{ url()->previous() }}"
            class="bg-purple-800 text-white py-2 px-4 rounded hover:bg-purple-700 text-sm cursor-pointer">
            back
        </a>
    </div>
    <h1 class="text-2xl font-semibold">Vendor Details</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    @if (session('error'))
    <div class="mb-4 text-orange-500 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Vendor Info --}}
    <div class="md:col-span-2 bg-white border border-slate-200 rounded-lg p-6">
        <h2 class="text-lg font-medium mb-4">Basic Information</h2>

        <div class="space-y-3 text-sm">
            <p><span class="font-medium text-slate-600">Shop Name:</span> {{ $vendor->vendorProfile->shop_name }}</p>
            <p><span class="font-medium text-slate-600">Shop Address:</span> {{ $vendor->vendorProfile->shop_address }}</p>
            <p><span class="font-medium text-slate-600">Shop Phone:</span> {{ $vendor->vendorProfile->phone }}</p>
            <p>
                <span class="font-medium text-slate-600">Status:</span>
                <span class="px-2 py-1 rounded text-white text-xs
                    @if($vendor->vendorProfile->status == 'approved') bg-green-600
                    @elseif($vendor->vendorProfile->status == 'pending') bg-yellow-500
                    @else bg-red-600 @endif">
                    {{ ucfirst($vendor->vendorProfile->status) }}
                </span>
            </p>
        </div>

        <div class="text-sm my-3">
            <form method="POST" action="{{ route('admin.vendors.update', $vendor) }}">
                @csrf
                @method('PUT')

                <select name="status"
                    class="border border-slate-300 rounded px-3 py-2 text-sm mb-4">
                    <option value="pending" @selected($vendor->vendorProfile->status=='pending')>Pending</option>
                    <option value="approved" @selected($vendor->vendorProfile->status=='approved')>Approved</option>
                    <option value="rejected" @selected($vendor->vendorProfile->status=='rejected')>Rejected</option>
                </select>

                <button
                    class=" bg-slate-800 text-white px-2 py-2 rounded hover:bg-slate-700 text-sm">
                    Update Status
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
