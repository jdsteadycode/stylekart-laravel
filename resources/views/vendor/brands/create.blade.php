@extends('vendor.layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Create Brand</h2>
        <p class="text-slate-500 text-sm">Register a new designer label or private boutique identity</p>
    </div>

    <form action="{{ route('vendor.brands.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Brand Name --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Brand Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full p-3 border rounded-xl outline-none transition {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-indigo-500' }}"
                placeholder="e.g. Urban Vogue or Heritage Silks">

            @error('name')
                <p class="text-red-500 text-[11px] mt-1 font-semibold uppercase tracking-wide">× {{ $message }}</p>
            @enderror
        </div>

        {{-- Brand Logo (Spatie) --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Brand Logo</label>
            <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                <input type="file" name="logo"
                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
            </div>

            @error('logo')
                <p class="text-red-500 text-[11px] mt-1 font-semibold uppercase tracking-wide">× {{ $message }}</p>
            @enderror
            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-tighter">Recommended: Square logo, max 2MB (JPG, PNG)</p>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Brand Story (Optional)</label>
            <textarea name="description" rows="4"
                class="w-full p-3 border border-slate-200 rounded-xl outline-none focus:border-indigo-500"
                placeholder="Describe the craft, style, or history behind this label..."></textarea>

            @error('description')
                <p class="text-red-500 text-[11px] mt-1 font-semibold uppercase tracking-wide">× {{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="bg-indigo-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                Create Label
            </button>
            <a href="{{ route('vendor.brands.index') }}" class="text-slate-400 hover:text-slate-600 text-sm font-bold transition">
                Discard
            </a>
        </div>
    </form>
</div>
@endsection
