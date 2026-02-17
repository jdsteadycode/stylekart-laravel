@extends('admin.layouts.app')


@section('content')

{{-- heading section --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Manage Vendors</h1>
</div>


{{-- Filter Section --}}
<div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

    <form method="GET" action="{{ route('admin.vendors.index') }}" class="flex flex-col md:flex-row gap-4 items-center">

        {{-- Status Filter --}}
        <div>
            <label for="status" class="text-sm text-gray-600 mr-2">Status:</label>
            <select
                    onchange="this.form.submit()"
                    name="status"
                    id="status"
                    class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

    </form>
</div>


<div class="bg-white border border-slate-200 rounded-lg overflow-hidden">

    @if (session('success'))
    <div class="mb-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="mb-4 text-orange-500 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('error') }}
    </div>
    @endif

    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($vendors as $vendor)
            <tr>
                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">
                    <a
                        href="{{ route('admin.vendors.show', $vendor) }}"
                    >
                        {{ $vendor->name }}
                    </a>
                </td>
                <td class="px-4 py-3">{{ $vendor->email }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-white
                        @if($vendor->vendorProfile->status == 'approved') bg-green-600
                        @elseif($vendor->vendorProfile->status == 'pending') bg-yellow-500
                        @else bg-red-600 @endif">
                        {{ ucfirst($vendor->vendorProfile->status ?? 'pending') }}
                    </span>
                </td>
                <td class="px-4 py-3 space-x-2" x-data="{ open: false }">

                    {{-- Approve/Reject --}}
                    @if($vendor->vendorProfile)
                        <form method="POST" action="{{ route('admin.vendors.update', $vendor) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="status"
                                class="border px-2 py-1 rounded text-sm"
                                onchange="this.form.submit()">
                                <option value="pending" @selected($vendor->vendorProfile->status=='pending')>Pending</option>
                                <option value="approved" @selected($vendor->vendorProfile->status=='approved')>Approved</option>
                                <option value="rejected" @selected($vendor->vendorProfile->status=='rejected')>Rejected</option>
                            </select>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                    No vendors found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
