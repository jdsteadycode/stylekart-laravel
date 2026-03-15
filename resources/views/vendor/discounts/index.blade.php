{{-- resources/views/vendor/discounts/index.blade.php --}}
@extends('vendor.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">My Discounts & Sales</h2>
        <a href="{{ route('vendor.discounts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            + Create New Discount
        </a>
    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 font-bold">Name</th>
                        <th class="py-3 px-6 font-bold">Discount</th>
                        <th class="py-3 px-6 font-bold">Applies To</th>
                        <th class="py-3 px-6 font-bold">Status</th>
                        <th class="py-3 px-6 font-bold">Valid Until</th>
                        <th class="py-3 px-6 font-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($discounts as $discount)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">

                            {{-- 1. Name --}}
                            <td class="py-3 px-6 text-left whitespace-nowrap font-medium">
                                {{ $discount->name }}
                            </td>

                            {{-- 2. Discount Value & Type --}}
                            <td class="py-3 px-6 text-left">
                                @if($discount->discount_type === 'percentage')
                                    <span class="text-green-600 font-bold">{{ round($discount->discount_value) }}% OFF</span>
                                @else
                                    <span class="text-green-600 font-bold">₹{{ round($discount->discount_value) }} OFF</span>
                                @endif
                            </td>

                            {{-- 3. What is on sale?  --}}
                            <td class="py-3 px-6 text-left">
                                @if($discount->product_id)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Product</span>
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $discount->product->name ?? 'Product Deleted' }}</span>
                                @elseif($discount->sub_category_id)
                                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Category</span>
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $discount->subCategory->name ?? 'Category Deleted' }}</span>
                                @else
                                    <span class="text-red-500 text-xs">Target Missing</span>
                                @endif
                            </td>

                            {{-- 4. Smart Status Badges --}}
                            <td class="py-3 px-6 text-left">
                                <form action="{{ route('vendor.discounts.toggle', ['discount' => $discount]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" class="transition-transform hover:scale-105 active:scale-95 focus:outline-none">
                                        @if(!$discount->is_active)
                                            {{-- Paused Badge --}}
                                            <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-bold flex items-center gap-1">
                                                ⚪ Paused
                                            </span>
                                        @elseif($discount->starts_at->isFuture())
                                            {{-- Upcoming Badge --}}
                                            <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold flex items-center gap-1">
                                                ⏳ Upcoming
                                            </span>
                                        @elseif($discount->ends_at->isPast())
                                            {{-- Expired Badge --}}
                                            <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs font-bold flex items-center gap-1">
                                                🚫 Expired
                                            </span>
                                        @else
                                            {{-- Active Live Badge --}}
                                            <span class="bg-green-100 text-green-600 py-1 px-3 rounded-full text-xs font-bold animate-pulse flex items-center gap-1">
                                                🟢 Active Live
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>


                            {{-- 5. Timers (Using Carbon) --}}
                            <td class="py-3 px-6 text-left">
                                <div class="text-xs">
                                    <span class="text-gray-500">Starts:</span> {{ $discount->starts_at->format('d M, h:i A') }}<br>
                                    <span class="text-gray-500">Ends:</span> <span class="font-bold text-gray-700">{{ $discount->ends_at->format('d M, h:i A') }}</span>
                                </div>
                            </td>

                            {{-- 6. Actions (Edit / Delete) --}}
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center gap-2">
                                    {{-- Edit Button (Stub for now) --}}
                                    <a href="{{ route('vendor.discounts.edit', ['discount' => $discount ]) }}" class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110" title="Edit">
                                        ✏️
                                    </a>

                                    {{-- Delete Button (JS Confirmation alert for now) --}}
                                    <form action="{{ route('vendor.discounts.destroy', ['discount' => $discount]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discount?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110" title="Delete">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                You haven't created any discounts yet. Click the button above to run your first sale!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if($discounts->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $discounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
