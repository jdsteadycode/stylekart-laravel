@extends('delivery-person.layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-5xl mx-auto">

    {{-- Simple Header --}}
    <div class="flex items-end justify-between mb-10">
        <div>
            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Order Notifications</h2>
            <p class="text-slate-400 text-xs">Real-time requests in your area</p>
        </div>

        @if(auth()->user()->unreadNotifications->isNotEmpty())
            <form action="{{ route('delivery.notifications.markRead') }}" method="POST">
                @csrf
                <button type="submit" class="text-[10px] font-bold text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition">
                    Mark All Read
                </button>
            </form>
        @endif
    </div>

    {{-- The Feed --}}
    <div class="space-y-2">
        @forelse($notifications as $notification)
            @php $isUnread = is_null($notification->read_at); @endphp

            <a href="{{ $notification->data['action_url'] ?? route('dashboard.delivery') }}"
               class="block group bg-white border border-slate-100 rounded-2xl p-4 transition-all {{ $isUnread ? 'ring-1 ring-indigo-50 shadow-sm' : 'opacity-60' }}">

                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        {{-- Status Dot --}}
                        <div class="h-2 w-2 rounded-full {{ $isUnread ? 'bg-orange-500 animate-pulse' : 'bg-slate-200' }}"></div>

                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition">
                                {{ $notification->data['city'] }} <span class="mx-1 text-slate-300">/</span> Order #{{ $notification->data['order_number'] }}
                            </p>
                            <p class="text-[10px] text-slate-400 font-medium">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Small Arrow --}}
                    <div class="text-slate-300 group-hover:text-indigo-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="py-20 text-center">
                <p class="text-3xl mb-4">🏜️</p>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No Alerts Found</p>
                <p class="text-xs text-slate-300 mt-1">New jobs will appear here automatically.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
