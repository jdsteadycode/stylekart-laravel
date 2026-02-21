@extends('vendor.layouts.app')

@php
    $status = auth()->user()->vendorProfile?->status;
@endphp

@section('content')
@if(session('success'))
    <div class="mb-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

@if($status === 'pending')
    <div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-3">
        ⏳ Your profile is under review.
    </div>
@endif

@if($status === 'rejected')
    <div class="bg-red-100 text-red-800 p-3 rounded mb-3">
        ❌ Your profile was rejected.
        <br>
        <strong>Reason:</strong>
        {{ auth()->user()->vendorProfile->rejection_reason }}
    </div>
@endif

@if($status === 'approved')
    <div class="bg-green-100 text-green-800 p-3 rounded mb-3">
        ✅ Your profile is approved.
    </div>
@endif



<div class="max-w-xl">
    <h1 class="text-2xl font-semibold mb-6">Edit Profile</h1>

    <x-vendor.profile-form
        action="{{ route('vendor.profile.update', $vendor, $vendorProfile) }}"
        :vendor="$vendor"
        :vendorProfile="$vendorProfile"
/>

</div>
@endsection
