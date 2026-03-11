@extends('admin.layouts.app')

@section('content')

{{-- when success message --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
        {{ session('success') }}
    </div>
@elseif($errors->any())
    @foreach($errors->all() as $error)
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ $error }}
        </div>
    @endforeach
@endif

<div class="bg-white rounded-lg shadow-sm p-6">
    <h1 class="text-2xl font-bold mb-6 text-slate-700">Assign Deliveries</h1>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 uppercase text-xs font-semibold">
                    <th class="p-4">Order #</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Amount</th>
                    <th class="p-4">Consolidated Status</th>
                    <th class="p-4">Assign Delivery Person</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-4 font-medium text-blue-600">{{ $order->order_number }}</td>
                    <td class="p-4">{{ $order->user->name }}</td>
                    <td class="p-4">₹{{ number_format($order->total_amount, 2) }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">Ready</span>
                    </td>
                    <td class="p-4">
                        {{-- Small Form to Assign --}}
                        <form action="{{ route('admin.deliveries.assign') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <select name="delivery_person_id" class="border border-slate-300 rounded px-3 py-1 text-sm focus:outline-blue-500" required>
                                <option value="">Select Person</option>
                                @foreach($deliveryPersons as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }} ({{ $person->deliveryProfile->vehicle_number ?? 'No Vehicle' }})</option>
                                @endforeach
                            </select>

                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm font-medium transition">
                                Assign
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-slate-500 italic">
                        No orders are currently consolidated and ready for delivery.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
