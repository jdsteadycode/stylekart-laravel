<x-guest-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}!</p>
        <p>Your role: {{ auth()->user()->role }}</p>

        <ul class="mt-4">
            <li><a href="#" class="text-blue-500 underline">Manage Categories</a></li>
            <li><a href="#" class="text-blue-500 underline">Manage Products</a></li>
            <li><a href="#" class="text-blue-500 underline">View Orders</a></li>
        </ul>

        <!-- logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</x-guest-layout>