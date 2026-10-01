<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Washly Laundry' }}</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN Fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 flex flex-col antialiased" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Brand Logo & Links -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform shrink-0">
                            <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="w-fit">
                            <span class="block font-extrabold text-xl tracking-tight bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">WASHLY</span>
                            <span class="block w-full text-center text-xs font-semibold text-blue-500 tracking-wider uppercase">Laundry</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <div class="hidden md:flex md:space-x-1">
                        @auth
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Dashboard</a>
                                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Pesanan</a>
                                <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Pelanggan</a>
                                <a href="{{ route('admin.drivers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.drivers.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Driver</a>
                                <a href="{{ route('admin.services.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Layanan</a>
                                <a href="{{ route('admin.payments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Pembayaran</a>
                                <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Laporan</a>
                            @elseif(auth()->user()->hasRole('driver'))
                                <a href="{{ route('driver.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('driver.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Dashboard Driver</a>
                                <a href="{{ route('driver.pickups') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('driver.pickups') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Tugas Pickup</a>
                                <a href="{{ route('driver.deliveries') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('driver.deliveries') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Tugas Delivery</a>
                            @else
                                <a href="{{ route('customer.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Dashboard</a>
                                <a href="{{ route('customer.orders.create') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('customer.orders.create') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Buat Pesanan</a>
                                <a href="{{ route('customer.orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('customer.orders.index') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}">Riwayat Order</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Right User Info & Actions -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    @auth
                        <!-- Role Badge -->
                        @if(auth()->user()->hasRole('admin'))
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-800 border border-cyan-200">
                                Outlet Admin
                            </span>
                        @elseif(auth()->user()->hasRole('driver'))
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 border border-sky-200">
                                Driver Lapangan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Customer
                            </span>
                        @endif

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-sm focus:outline-none p-1.5 rounded-lg hover:bg-slate-100">
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-700">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-40">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs text-slate-400">Login sebagai</p>
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600">Pengaturan Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">Log Out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">Daftar Sekarang</a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-4 space-y-2">
            @auth
                <div class="py-2 border-b border-slate-100 mb-2">
                    <p class="font-bold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Dashboard</a>
                    <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Pesanan</a>
                    <a href="{{ route('admin.customers.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Pelanggan</a>
                    <a href="{{ route('admin.drivers.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Driver</a>
                    <a href="{{ route('admin.services.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Layanan</a>
                    <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Pembayaran</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Laporan</a>
                @elseif(auth()->user()->hasRole('driver'))
                    <a href="{{ route('driver.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Dashboard Driver</a>
                    <a href="{{ route('driver.pickups') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Tugas Pickup</a>
                    <a href="{{ route('driver.deliveries') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Tugas Delivery</a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Dashboard</a>
                    <a href="{{ route('customer.orders.create') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Buat Pesanan</a>
                    <a href="{{ route('customer.orders.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Riwayat Order</a>
                @endif
                <a href="{{ route('profile') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Profil Saya</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-rose-600 hover:bg-rose-50">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-blue-50">Masuk</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-blue-600 hover:bg-blue-50">Daftar Akun Baru</a>
            @endauth
        </div>
    </nav>

    <!-- Global Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if (session()->has('message'))
            <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 rounded-md overflow-hidden shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                </div>
                <span class="font-bold text-slate-700 text-sm">Washly Laundry System</span>
                <span class="text-xs text-slate-400">| Single Outlet Pickup & Delivery</span>
            </div>
            <p class="text-xs text-slate-400 text-center">
                &copy; {{ date('Y') }} Washly Laundry UMKM. All rights reserved. Demo QRIS Payment System.
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
