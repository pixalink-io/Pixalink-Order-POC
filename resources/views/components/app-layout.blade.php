<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Restaurant Menu') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 antialiased">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <a href="{{ route('menu.index') }}" class="flex items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name', 'Restaurant') }}</h1>
                            <p class="text-sm text-gray-600">Order your favorites</p>
                        </div>
                    </a>

                    <!-- Cart Button -->
                    <button onclick="Livewire.dispatch('openCart')"
                        class="relative p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span id="cart-count-badge"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                            0
                        </span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Cart Drawer -->
        <livewire:cart-drawer />

        <!-- Footer -->
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-gray-600 text-sm">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    @livewireScripts

    <script>
        // Update cart count badge
        document.addEventListener('livewire:init', () => {
            Livewire.on('cartCountUpdated', (event) => {
                document.getElementById('cart-count-badge').textContent = event.count;
            });
        });
    </script>
</body>

</html>
