@extends('admin.layouts.app')

@section('content')
<div class="max-w-xl">

    <h1 class="text-2xl font-semibold mb-6">Add Category</h1>

   <x-admin.category-form
       action="{{ route('admin.categories.store') }}"
   />

</div>
@endsection
