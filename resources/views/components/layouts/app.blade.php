<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100 dark">
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
    <!-- Leaflet CSS & JS for Interactive Location Picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- HTML2Canvas JS for Downloading Receipt as JPG Image -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <!-- Midtrans Snap JS Payment Gateway -->
    <script type="text/javascript"
        src="{{ config('midtrans.is_production', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key', '') }}">
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .leaflet-container { font-family: 'Inter', sans-serif; z-index: 10; }

        /* Modern Dark Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #090d16; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }

        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-print-area, #receipt-print-area * {
                visibility: visible;
            }
            #receipt-print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 480px;
                margin: 0 auto;
                padding: 12px;
                background: #0f172a !important;
                color: #f8fafc !important;
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 flex flex-col antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Premium Dark Navigation Bar -->
    <nav class="bg-slate-900/90 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-30 shadow-2xl shadow-slate-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Brand Logo & Links -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-lg shadow-blue-500/30 ring-1 ring-blue-500/40 group-hover:scale-105 transition-all shrink-0 bg-slate-900 p-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="w-fit">
                            <span class="block font-extrabold text-xl tracking-tight bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">WASHLY</span>
                            <span class="block w-full text-center text-[10px] font-bold text-cyan-400 tracking-widest uppercase">Laundry</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <div class="hidden md:flex md:space-x-1.5">
                        @auth
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Dashboard</a>
                                <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Pesanan</a>
                                <a href="{{ route('admin.customers.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.customers.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Pelanggan</a>
                                <a href="{{ route('admin.drivers.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.drivers.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Driver</a>
                                <a href="{{ route('admin.admins.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.admins.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Admin</a>
                                <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.services.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Layanan</a>
                                <a href="{{ route('admin.payments.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Pembayaran</a>
                                <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Laporan</a>
                                <a href="{{ route('admin.promotions.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.promotions.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Promosi</a>
                            @elseif(auth()->user()->hasRole('driver'))

                                <a href="{{ route('driver.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('driver.dashboard') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Dashboard Driver</a>
                                <a href="{{ route('driver.pickups') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('driver.pickups') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Tugas Pickup</a>
                                <a href="{{ route('driver.deliveries') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('driver.deliveries') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Tugas Delivery</a>
                            @else
                                <a href="{{ route('customer.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('customer.dashboard') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Dashboard</a>
                                <a href="{{ route('customer.orders.create') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('customer.orders.create') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Buat Pesanan</a>
                                <a href="{{ route('customer.orders.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('customer.orders.index') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">Riwayat Order</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Right User Info & Actions -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    @auth
                        <!-- Role Badge -->
                        @if(auth()->user()->hasRole('admin'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                Outlet Admin
                            </span>
                        @elseif(auth()->user()->hasRole('driver'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                                Driver Lapangan
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                Customer
                            </span>
                        @endif

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2.5 text-sm focus:outline-none p-1.5 rounded-xl hover:bg-slate-800/70 border border-transparent hover:border-slate-700/80 transition-all">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center shadow-md shadow-blue-500/20">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-200">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-52 bg-slate-900/95 backdrop-blur-2xl rounded-2xl shadow-2xl border border-slate-800 py-1.5 z-40">
                                <div class="px-4 py-2.5 border-b border-slate-800">
                                    <p class="text-xs text-slate-400">Login sebagai</p>
                                    <p class="text-sm font-semibold text-slate-200 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">Pengaturan Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-rose-400 hover:bg-rose-500/10 transition-colors">Log Out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-lg shadow-blue-500/25 transition-all">Daftar Sekarang</a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden border-b border-slate-800 bg-slate-900/95 px-4 pt-2 pb-4 space-y-2">
            @auth
                <div class="py-2.5 border-b border-slate-800 mb-2">
                    <p class="font-bold text-slate-100">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                </div>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Dashboard</a>
                    <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Pesanan</a>
                    <a href="{{ route('admin.customers.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Pelanggan</a>
                    <a href="{{ route('admin.drivers.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Driver</a>
                    <a href="{{ route('admin.admins.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Admin</a>
                    <a href="{{ route('admin.services.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Layanan</a>
                    <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Pembayaran</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Laporan</a>
                    <a href="{{ route('admin.promotions.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Promosi</a>
                @elseif(auth()->user()->hasRole('driver'))
                    <a href="{{ route('driver.dashboard') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Dashboard Driver</a>
                    <a href="{{ route('driver.pickups') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Tugas Pickup</a>
                    <a href="{{ route('driver.deliveries') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Tugas Delivery</a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Dashboard</a>
                    <a href="{{ route('customer.orders.create') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Buat Pesanan</a>
                    <a href="{{ route('customer.orders.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Riwayat Order</a>
                @endif
                <a href="{{ route('profile') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Profil Saya</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-base font-medium text-rose-400 hover:bg-rose-500/10">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Masuk</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-xl text-base font-semibold text-blue-400 hover:bg-slate-800">Daftar Akun Baru</a>
            @endauth
        </div>
    </nav>

    <!-- Global Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if (session()->has('message'))
            <div class="p-4 mb-4 rounded-2xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 flex items-center justify-between shadow-lg backdrop-blur-md">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 mb-4 rounded-2xl bg-rose-950/60 border border-rose-800/80 text-rose-300 flex items-center justify-between shadow-lg backdrop-blur-md">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <footer class="bg-slate-900/60 border-t border-slate-800/80 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-lg overflow-hidden shrink-0 bg-slate-950 p-0.5 border border-slate-800">
                    <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                </div>
                <span class="font-bold text-slate-200 text-sm">Washly Laundry System</span>
                <span class="text-xs text-slate-500">| Single Outlet Pickup & Delivery</span>
            </div>
            <p class="text-xs text-slate-400 text-center">
                &copy; {{ date('Y') }} Washly Laundry UMKM. All rights reserved. Demo QRIS Payment System.
            </p>
        </div>
    </footer>

    <!-- Floating Admin WhatsApp Chat Button (Khusus Customer di Jam Kerja) -->
    @auth
        @if(auth()->user()->hasRole('customer'))
            @php
                $adminPhone = '081234567890';
                try {
                    if (\Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
                        $adminUser = \App\Models\User::role('admin')->first();
                        if ($adminUser && !empty($adminUser->phone)) {
                            $adminPhone = $adminUser->phone;
                        }
                    }
                } catch (\Throwable $e) {
                    // Fallback to default admin phone if role doesn't exist yet
                }
                $cleanPhone = preg_replace('/[^0-9]/', '', $adminPhone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
                $waText = urlencode("Halo Admin Washly Laundry, saya ingin bertanya tentang layanan laundry.");
                $waUrl = "https://wa.me/{$cleanPhone}?text={$waText}";
            @endphp

            <div class="fixed bottom-6 right-6 z-50 flex items-center gap-3">
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" 
                   class="group flex items-center gap-3 bg-slate-900/90 hover:bg-slate-800 border border-emerald-500/40 hover:border-emerald-400 p-2.5 pr-4 rounded-full shadow-2xl shadow-emerald-500/20 backdrop-blur-xl transition-all duration-300 hover:scale-105">
                    <div class="relative w-11 h-11 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/30 shrink-0">
                        <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 ring-2 ring-slate-950"></span>
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-white tracking-wide">Chat Admin WA</span>
                            <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20">Jam Kerja</span>
                        </div>
                        <span class="text-[11px] text-slate-400 group-hover:text-slate-300 transition-colors">Respon Jam Kerja (08.00 - 20.00)</span>
                    </div>
                </a>
            </div>
        @endif
    @endauth

    <!-- Customer Promotional Pop-up Modal -->
    @auth
        @if(auth()->user()->hasRole('customer'))
            @php
                $activePromo = null;
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('promotions')) {
                        $activePromo = \App\Models\Promotion::where('is_active', true)->latest()->first();
                    }
                } catch (\Throwable $e) {
                    // Fallback if DB table not found
                }
            @endphp

            @if($activePromo)
                <div x-data="{ openPromo: !sessionStorage.getItem('dismissed_promo_{{ $activePromo->id }}') }"
                     x-show="openPromo"
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/80 backdrop-blur-md">
                    
                    <div class="relative w-full max-w-2xl bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden p-6 sm:p-8 space-y-6">
                        <!-- Top Header: Badge & Close X Button -->
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black rounded-full uppercase tracking-wider shadow-md shadow-blue-500/20">
                                {{ $activePromo->badge ?? 'PROMO LAUNDRY' }}
                            </span>
                            <button @click="openPromo = false; sessionStorage.setItem('dismissed_promo_{{ $activePromo->id }}', 'true')" 
                                    class="w-9 h-9 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all focus:outline-none border border-slate-700/60 font-black">
                                &#x2715;
                            </button>
                        </div>

                        <!-- Headline Title -->
                        <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight tracking-tight">
                            {{ $activePromo->title }}
                        </h2>

                        <!-- Promo Banner / Image Graphic -->
                        <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-lg bg-slate-950/60 max-h-60 sm:max-h-72 flex items-center justify-center">
                            <img src="{{ asset($activePromo->image ?? 'images/logo-welcome.png') }}" 
                                 alt="{{ $activePromo->title }}" 
                                 class="w-full h-full object-cover">
                        </div>

                        <!-- Promo Description -->
                        <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                            {{ $activePromo->description }}
                        </p>

                        <!-- Promo Code & Action Box -->
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-800/80">
                            @if($activePromo->promo_code)
                                <div class="w-full sm:w-auto flex items-center space-x-2 px-4 py-2.5 rounded-2xl bg-slate-950 border border-dashed border-blue-500/40 text-xs">
                                    <span class="text-slate-400">Kode Promo:</span>
                                    <strong class="font-mono text-cyan-400 text-sm font-extrabold uppercase tracking-wider">{{ $activePromo->promo_code }}</strong>
                                </div>
                            @endif

                            <div class="w-full sm:w-auto flex items-center justify-end gap-3">
                                <button @click="openPromo = false; sessionStorage.setItem('dismissed_promo_{{ $activePromo->id }}', 'true')" 
                                        class="px-5 py-3 rounded-2xl text-xs sm:text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-all">
                                    Tutup Info
                                </button>
                                <a href="{{ route('customer.orders.create') }}" 
                                   @click="sessionStorage.setItem('dismissed_promo_{{ $activePromo->id }}', 'true')"
                                   class="px-6 py-3 rounded-2xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-xl shadow-blue-500/30 transition-all hover:scale-105">
                                    Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endauth



    <script>
        function downloadReceiptAsJpg(orderNumber) {
            const element = document.getElementById('receipt-print-area');
            if (!element) {
                alert('Area resi tidak ditemukan!');
                return;
            }

            html2canvas(element, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `Resi-Washly-${orderNumber || 'Laundry'}.jpg`;
                link.href = canvas.toDataURL('image/jpeg', 0.95);
                link.click();
            }).catch(err => {
                alert('Gagal membuat gambar resi: ' + err.message);
            });
        }

        function triggerMidtransPay(snapToken, orderNumber) {
            if (!snapToken) {
                alert('Token pembayaran tidak valid.');
                return;
            }

            if (snapToken.startsWith('SNAP-TEST-') || snapToken.startsWith('SNAP-SANDBOX-')) {
                const confirmSandbox = confirm('KONFIRMASI PEMBAYARAN LAUNDRY\n\nNo. Order: ' + (orderNumber || 'Washly') + '\n\nApakah Anda ingin menyelesaikan pembayaran tagihan laundry ini sekarang?');
                if (confirmSandbox && window.Livewire) {
                    const activeComp = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                    if (activeComp) {
                        activeComp.call('processGatewayPayment');
                    }
                }
                return;
            }

            if (typeof window.snap !== 'undefined') {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        alert('Pembayaran Laundry Berhasil!');
                        if (window.Livewire) {
                            const activeComp = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                            if (activeComp) activeComp.call('processGatewayPayment');
                        }
                    },
                    onPending: function(result) {
                        alert('Pembayaran Menunggu Penyelesaian.');
                        window.location.reload();
                    },
                    onError: function(result) {
                        alert('Pembayaran Gagal / Dibatalkan.');
                    },
                    onClose: function() {
                        console.log('Customer menutup dialog pembayaran.');
                    }
                });
            } else {
                const confirmFallback = confirm('KONFIRMASI PEMBAYARAN LAUNDRY\n\nNo. Order: ' + (orderNumber || 'Washly') + '\n\nApakah Anda ingin menyelesaikan pembayaran tagihan laundry ini sekarang?');
                if (confirmFallback && window.Livewire) {
                    const activeComp = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                    if (activeComp) {
                        activeComp.call('processGatewayPayment');
                    }
                }
            }
        }
    </script>

    @livewireScripts
</body>
</html>
