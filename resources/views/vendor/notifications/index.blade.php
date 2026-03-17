@extends('vendor.layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Your Notifications</h2>

        {{-- Button to clear the "Red Dot" --}}
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('vendor.notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @forelse(auth()->user()->notifications as $notification)
            <div class="p-4 border-b last:border-0 {{ $notification->read_at ? 'opacity-60' : 'bg-indigo-50/30' }}">
                <div class="flex justify-between items-start">
                    <div>
                        {{-- 1. Show the generic message --}}
                        <p class="text-slate-900 font-medium">
                            {{ $notification->data['message'] }}
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            {{-- 2. Check if it's an Order Notification --}}
                            @if(isset($notification->data['order_number']))
                                Order: <span class="font-mono">{{ $notification->data['order_number'] }}</span>

                            {{-- 3. Check if it's a Low Stock Notification --}}
                            @elseif(isset($notification->data['current_stock']))
                                Stock Remaining: <span class="font-bold text-rose-600">{{ $notification->data['current_stock'] }}</span>
                            @endif

                            • {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    @if(!$notification->read_at)
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <span class="text-4xl">📭</span>
                <p class="mt-2 text-slate-500">No notifications yet!</p>
            </div>
        @endforelse

    </div>
</div>
@endsection
