<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StyleKart | Vendor Panel</title>

    {{-- Tailwind / Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-indigo-50 text-slate-800">

    {{-- Vendor Top Navigation --}}
    @include('vendor.partials.navigationbar')

    <div class="flex min-h-screen">

        {{-- Vendor Sidebar --}}
        @include('vendor.partials.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

</body>

</html>
