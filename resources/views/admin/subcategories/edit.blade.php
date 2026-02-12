@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl">

    <h1 class="text-2xl font-semibold mb-6">
        Edit Sub Category
    </h1>

    <x-admin.subcategory-form
        action="{{ route('admin.subcategories.update', $subcategory) }}"
        :subcategory="$subcategory"
        method="PUT"
    />
</div>
@endsection
