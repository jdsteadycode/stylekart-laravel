@extends('admin.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Manage Categories</h1>

    <a href="{{ route('admin.categories.create') }}"
        class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800">
        ➕ Add Category
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-lg overflow-hidden">

    @if (session('success'))
    <div class="mb-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
    </div>
    @endif


    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Category Name</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($categories as $category)
            <tr>
                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                <td class="px-4 py-3 space-x-3" x-data="{ open: false }">

                    <a href="{{ route('admin.categories.edit', $category) }}"
                        class="text-yellow-600 hover:underline">
                        Edit
                    </a>

                    <x-admin.delete-modal
                        action="{{ route('admin.categories.destroy', $category) }}"
                        title="{{ 'Are you sure?'}}"
                        message="{{ 'To delete ' . $category->name }}"
                    />
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-6 text-center text-slate-500">
                    No categories found.
                </td>
            </tr>
            @endforelse
        </tbody>

    </table>

</div>
@endsection
