@extends('admin.layouts.app')

@section('content')
<h1 class="text-2xl font-semibold">Admin Dashboard</h1>
<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    <!-- Categories -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Categories</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

    <!-- Subcategories -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Subcategories</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

    <!-- Customers -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Customers</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

    <!-- Vendors -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Vendors</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

    <!-- Products -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Products</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Total Revenue</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

    <!-- Total Sales -->
    <div class="bg-white p-5 rounded-lg border border-slate-200">
        <div class="text-sm text-slate-500">Total Sales</div>
        <div class="mt-2 text-2xl font-bold">0</div>
    </div>

</div>

@endsection