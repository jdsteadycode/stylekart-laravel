@extends('vendor.layouts.app')

@section('content')
@if(session('success'))
    <div class="mb-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
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
