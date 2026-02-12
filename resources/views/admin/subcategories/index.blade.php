@extends('admin.layouts.app')


@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Manage Sub Categories</h1>

    <a href="{{ route('admin.subcategories.create') }}"
        class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800">
        ➕ Add Sub Category
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-lg overflow-hidden">

    @if (session('success'))
    <div class="m-4 text-green-700 bg-green-100 px-4 py-2 rounded-md text-sm">
        {{ session('success') }}
    </div>
    @endif

    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Sub Category</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($subcategories as $subcategory)
            <tr>
                <td class="px-4 py-3">{{ $loop->iteration }}</td>

                <td class="px-4 py-3 font-medium">
                    {{ $subcategory->name }}
                </td>

                <td class="px-4 py-3 text-slate-600">
                    {{ $subcategory->category->name }}
                </td>

                <td class="px-4 py-3 space-x-2" x-data="{ open: false }">
                    <a href="{{ route('admin.subcategories.edit', $subcategory) }}"
                        class="text-yellow-600 hover:underline">
                        Edit
                    </a>

                    {{-- delete --}}
                    <x-admin.delete-modal
                        action="{{ route('admin.subcategories.destroy', $subcategory) }}"
                        title="{{ __('Delete subcategory?') }}"
                        message="{{ 'delete ' . $subcategory->name }}"
                    />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-slate-500">
                    No sub categories found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
