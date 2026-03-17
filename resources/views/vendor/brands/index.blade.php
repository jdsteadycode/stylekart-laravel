@extends('vendor.layouts.app')

@section('content')
    {{-- Success: Soft Indigo/Emerald --}}
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between bg-emerald-50 border border-emerald-100 px-5 py-3 rounded-2xl">
            <div class="flex items-center gap-3">
                <span class="text-emerald-500 text-sm">✓</span>
                <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-widest">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Error: Soft Rose/Amber --}}
    @if(session('error'))
        <div class="mb-6 flex items-center justify-between bg-rose-50 border border-rose-100 px-5 py-3 rounded-2xl">
            <div class="flex items-center gap-3">
                <span class="text-rose-500 text-sm">!</span>
                <p class="text-[11px] font-bold text-rose-800 uppercase tracking-widest">{{ session('error') }}</p>
            </div>
        </div>
    @endif


<div class="p-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">My Fashion Labels</h2>
            <p class="text-slate-500 text-sm">Manage your private brands and boutique identities</p>
        </div>
        <a href="{{ route('vendor.brands.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
            + New Label
        </a>
    </div>

    <div class="grid gap-4">
        @forelse($brands as $brand)
            <div class="bg-white border border-slate-100 p-4 rounded-2xl flex items-center justify-between group hover:border-indigo-100 transition">
                <div class="flex items-center gap-5">
                    {{-- Spatie Media Logo --}}
                    <div class="h-14 w-14 rounded-xl bg-slate-50 border border-slate-100 overflow-hidden flex items-center justify-center">
                        @if($brand->hasMedia('brand_logos'))
                            <img src="{{ $brand->getFirstMediaUrl('brand_logos') }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-xs font-bold text-slate-300">NO LOGO</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-900">{{ $brand->name }}</h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-bold uppercase tracking-widest">
                                {{ $brand->slug }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('vendor.brands.edit', $brand->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                        Edit
                    </a>

                    {{-- Delete Form --}}
                        <form action="{{ route('vendor.brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Are you sure? Products using this brand will become unbranded.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition">
                                Delete
                            </button>
                        </form>
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-4xl mb-4">🏷️</p>
                <h3 class="text-slate-500 font-bold uppercase tracking-widest">No Labels Found</h3>
                <p class="text-slate-400 text-xs mt-1">Start by creating your first designer label or boutique brand.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
