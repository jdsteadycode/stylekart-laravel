@extends('customer.layouts.app')

@section('title', 'My Account')

@section('content')
{{-- Toast for profile view --}}
@if(session('success') || session('error'))
    <div
        id="toast"
        class="fixed top-6 right-6 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-bold transition-all duration-500"
        style="background-color: {{ session('success') ? '#16a34a' : '#dc2626' }};"
    >
        @if(session('success'))
            🛍️ {{ session('success') }}
        @else
            ⚠️ {{ session('error') }}
        @endif
    </div>

    <script>
        // Hide toast after 3 seconds
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-[-20px]');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
    </script>
@endif
<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg"
                        style="background-color: {{ $avatarColor }}"
                        >
                            {{ $firstLetter ?? 'N/A' }}
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 leading-tight">{{ $customer->name ??'N/A' }}</h2>
                            <p class="text-rose-400 text-[10px] font-black uppercase tracking-[0.2em]">STYLEKART CUSTOMER</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fa-regular fa-envelope text-rose-300 text-sm"></i>
                            <span class="text-sm font-medium">{{ $customer->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fa-solid fa-calendar-days text-rose-300 text-xs"></i>
                            <span class="text-sm font-medium">
                                Since {{ $customer->created_at->format('d M, Y') ?? 'DD-MM-YYYY' }}
                            </span>
                        </div>
                    </div>

                    <button
                        onclick="toggleModal('editProfileModal')"
                        class="w-full mt-8 py-3 bg-rose-50/50 text-rose-500 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all duration-300">
                        Edit Profile
                    </button>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-black text-gray-900 uppercase text-[10px] tracking-widest">My Addresses 🏠</h3>
                        <a href="{{ route('customer.address.create') }}" class="text-rose-500 text-[10px] font-black uppercase">+ Add New</a>
                    </div>

                    <div class="space-y-4">
                        @if($addresses->count())
                            @foreach($addresses as $address)
                            <div class="p-4 rounded-2xl border border-rose-100 bg-rose-50/30 relative group">

                                {{-- Default badge --}}
                                @if($address->is_default)
                                    <span class="absolute top-3 right-3 text-[8px] font-black bg-rose-500 text-white px-2 py-0.5 rounded-full uppercase">
                                        Default
                                    </span>
                                @endif

                                {{-- Action buttons container --}}
                                <div class="absolute top-3 right-3 flex items-center gap-3
                                            opacity-0 group-hover:opacity-100 transition-opacity">

                                    {{-- Edit --}}
                                    <a href="{{ route('customer.address.edit', $address) }}"
                                       class="text-gray-400 hover:text-rose-500 text-sm">
                                        ✏️
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('customer.address.destroy', $address) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-gray-400 hover:text-red-500 text-sm">
                                            🗑️
                                        </button>
                                    </form>
                                </div>

                                {{-- Address details --}}
                                <p class="text-[10px] font-black text-gray-400 mb-1 uppercase tracking-tighter italic">
                                    {{ ucfirst($address->address_type) ?? 'N/A' }}
                                </p>

                                <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                    {{ $address->address_line ?? 'N/A' }}<br>
                                    {{ $address->city ?? 'N/A'}}, {{ $address->state ?? 'N/A' }} - {{ $address->pincode ?? 'N/A' }}
                                </p>

                            </div>
                            @endforeach
                        @else
                            <span>No Addresses exist.</span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm min-h-full">
                    <div class="flex justify-between items-center mb-10 border-b border-rose-50 pb-6">
                        <h3 class="text-2xl font-black text-gray-900">Recent Orders 📦</h3>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total: {{ $recentOrdersCount ?? 'N/A' }} Orders</span>
                    </div>

                    <div class="space-y-4">
                        @if($recentOrdersCount)
                            @foreach($recentOrders as $order)
                            <div class="border border-rose-50 rounded-2xl p-6 hover:border-rose-200 transition-all">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-300 text-lg">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">ID: {{ $order->order_number ?? 'N/A' }}</p>
                                            <p class="text-sm font-bold text-gray-800">Placed on {{ $order->created_at->format('d M, Y') ?? 'DD-MM-YYYY' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-4 md:pt-0">
                                        <div class="text-left md:text-right">
                                            <!--<p class="text-lg font-black text-gray-900 leading-none">₹2,499</p>-->
                                            <!--<span class="text-[9px] font-black uppercase tracking-widest text-green-500">Delivered ✨</span>-->
                                        </div>
                                        <a
                                            role=       "button"
                                            href="{{-- route('customer.order-detail') --}}"
                                            class="bg-rose-50 text-rose-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">
                                            Order Details
                                        </a>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                        @else
                            <span>{{ "Seems like " . $customer->name . "Hasn't placed any order yet!" }}</span>
                        @endif
                    </div>

                    <p class="text-center text-[10px] font-bold text-gray-300 uppercase tracking-[0.3em] mt-12">
                        End of recent history
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- modal -->
 <div id="editProfileModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white w-full max-w-lg rounded-[40px] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">

            <button onclick="toggleModal('editProfileModal')" class="absolute top-6 right-6 text-gray-400 hover:text-rose-500 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <div class="p-10 pb-4 text-center">
                <div class="w-16 h-16 rounded-3xl flex items-center justify-center text-white text-2xl font-black mx-auto mb-4 shadow-xl"
                style="background-color: {{ $avatarColor }}"
                >
                    {{ $firstLetter ?? 'N/A' }}
                </div>
                <h3 class="text-2xl font-black text-gray-900">Update Profile</h3>
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.2em] mt-1">Refine your identity</p>
            </div>

            <form action="{{ route('customer.profile.update') }}" method="POST" class="p-10 pt-4 space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1 mb-2 block italic">Display Name</label>
                    <input type="text" name="name" value="{{ $customer->name ?? 'N/A' }}"
                        class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all">
                </div>

                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1 mb-2 block italic">Email Contact</label>
                    <input type="email" name="email" value="{{ $customer->email }}"
                        class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all">
                </div>

                <div class="pt-6 flex flex-col gap-3">
                    <button type="submit" class="w-full bg-rose-500 text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-rose-100 hover:bg-rose-600 transition-all active:scale-95">
                        Save Changes
                    </button>
                    <button type="button" onclick="toggleModal('editProfileModal')" class="w-full py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
                        Keep current info
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // () -> open/close the modal
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }
</script>

@endsection
