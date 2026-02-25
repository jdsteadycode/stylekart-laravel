@extends('customer.layouts.app')

@section('title', 'My Account')

@section('content')
<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-rose-500 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-rose-100">
                            AS
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 leading-tight">Arjun Sharma</h2>
                            <p class="text-rose-400 text-[10px] font-black uppercase tracking-[0.2em]">Verified Customer</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fa-regular fa-envelope text-rose-300 text-sm"></i>
                            <span class="text-sm font-medium">arjun@example.com</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i class="fa-solid fa-phone text-rose-300 text-xs"></i>
                            <span class="text-sm font-medium">+91 98765 43210</span>
                        </div>
                    </div>

                    <button class="w-full mt-8 py-3 bg-rose-50/50 text-rose-500 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all duration-300">
                        Edit Profile
                    </button>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-black text-gray-900 uppercase text-[10px] tracking-widest">My Addresses 🏠</h3>
                        <button class="text-rose-500 text-[10px] font-black uppercase">+ Add New</button>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl border border-rose-100 bg-rose-50/30 relative">
                            <span class="absolute top-3 right-3 text-[8px] font-black bg-rose-500 text-white px-2 py-0.5 rounded-full uppercase">Default</span>
                            <p class="text-[10px] font-black text-gray-400 mb-1 uppercase tracking-tighter italic">Primary Residence</p>
                            <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                123, Rose Villa, Pink City,<br>
                                Jaipur, Rajasthan - 302001
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-3xl border border-rose-50 shadow-sm min-h-full">
                    <div class="flex justify-between items-center mb-10 border-b border-rose-50 pb-6">
                        <h3 class="text-2xl font-black text-gray-900">Recent Orders 📦</h3>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total: 3 Orders</span>
                    </div>

                    <div class="space-y-4">
                        @for($i=1; $i<=3; $i++)
                        <div class="border border-rose-50 rounded-2xl p-6 hover:border-rose-200 transition-all">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-300 text-lg">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">ID: #SK-99{{$i}}</p>
                                        <p class="text-sm font-bold text-gray-800">Placed on 21 Feb, 2026</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-4 md:pt-0">
                                    <div class="text-left md:text-right">
                                        <!--<p class="text-lg font-black text-gray-900 leading-none">₹2,499</p>-->
                                        <!--<span class="text-[9px] font-black uppercase tracking-widest text-green-500">Delivered ✨</span>-->
                                    </div>
                                    <a
                                        role="button"
                                        href="{{-- route('customer.order-detail') --}}"
                                        class="bg-rose-50 text-rose-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">
                                        Order Details
                                    </a>
                                </div>

                            </div>
                        </div>
                        @endfor
                    </div>

                    <p class="text-center text-[10px] font-bold text-gray-300 uppercase tracking-[0.3em] mt-12">
                        End of recent history
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
