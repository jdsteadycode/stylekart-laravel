@extends('customer.layouts.app')

@section('title', 'Manage Address')

@section('content')
<div class="bg-rose-50/20 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('customer.profile') }}" class="text-[10px] font-black text-rose-400 uppercase tracking-widest flex items-center gap-2 hover:text-rose-600 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to Profile
            </a>
            <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">Shipping Address</h1>
        </div>

        <div class="bg-white rounded-[40px] border border-rose-50 shadow-sm overflow-hidden">
            <form action="{{ route('customer.address.store') }}" method="POST" class="p-8 md:p-12">
                @csrf
                @method('POST')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Receiver's Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="e.g. Arjun Sharma"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all @error('name') border-rose-500 @enderror">
                        @error('name') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            placeholder="e.g. +91 98765 43210"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all @error('phone') border-rose-500 @enderror">
                        @error('phone') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Full Address (House, Street, Area)</label>
                        <textarea name="address_line" rows="3"
                            placeholder="e.g. Flat 402, Rose Villa, 12th Main Road, Pink City"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-3xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all @error('address_line') border-rose-500 @enderror">{{ old('address_line') }}</textarea>
                        @error('address_line') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Landmark (Optional)</label>
                        <input type="text" name="landmark" value="{{ old('landmark') }}"
                            placeholder="e.g. Near Rose Garden or Behind the Post Office"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">City</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                            placeholder="e.g. Jaipur"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all @error('city') border-rose-500 @enderror">
                        @error('city') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">State</label>
                        <input type="text" name="state" value="{{ old('state') }}"
                            placeholder="e.g. Rajasthan"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all @error('state') border-rose-500 @enderror">
                        @error('state') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Pincode</label>
                        <input type="text" name="pincode" value="{{ old('pincode') }}"
                            placeholder="e.g. 302001"
                            class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all @error('pincode') border-rose-500 @enderror">
                        @error('pincode') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block italic">Address Type</label>
                        <select name="address_type" class="w-full bg-rose-50/30 border border-rose-50 rounded-2xl px-5 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-300 transition-all"
                        >
                            <option value="Where to?" hidden>Where to?</option>
                            <option value="home" {{ old('address_type') === 'home' ? 'selected' : ''}}>🏠 Home (Delivery anytime)</option>
                            <option value="office" {{ old('address_type') === 'office' ? 'selected' : ''}}>🏢 Office (10 AM - 6 PM)</option>
                            <option value="other" {{ old('address_type') === 'other' ? 'selected' : ''}}>📍 Other</option>
                        </select>
                        @error('address_type') <p class="mt-2 text-[9px] font-bold text-rose-500 uppercase tracking-tight ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-between p-4 bg-rose-50/20 rounded-2xl border border-rose-50">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-star text-rose-400 text-xs"></i>
                        <span class="text-[11px] font-black text-gray-600 uppercase tracking-widest">Set as default shipping address</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                    </label>
                </div>

                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-rose-500 text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-rose-100 hover:bg-rose-600 active:scale-95 transition-all">
                        Save Address
                    </button>
                    <a href="{{ route('customer.profile') }}" class="flex-1 bg-white border border-rose-100 text-gray-400 py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] text-center hover:bg-rose-50 transition-all">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
