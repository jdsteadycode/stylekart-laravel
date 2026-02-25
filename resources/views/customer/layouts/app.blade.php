<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stylekart | @yield('title', 'Your Fashion Destination')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Tailwind / Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

    {{-- header section --}}
    @include('customer.partials.header')

    {{-- main section --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- footer section --}}
    @include('customer.partials.footer')

</body>
</html>
