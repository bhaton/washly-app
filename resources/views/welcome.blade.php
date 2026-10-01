<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Washly Laundry - Layanan Antar Jemput Laundry Praktis & Transparan</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Header / Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200/60 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl overflow-hidden shadow-lg shadow-blue-500/25 shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="w-fit">
                        <span class="block font-black text-2xl tracking-tight bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">WASHLY</span>
                        <span class="block w-full text-center text-xs font-bold text-blue-500 tracking-widest uppercase">Laundry</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-1">
                    <a href="#harga" class="px-3 py-2 rounded-lg text-sm font-bold text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors">Layanan</a>
                    <a href="#alur-pemesanan" class="px-3 py-2 rounded-lg text-sm font-bold text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors">Cara Pemesanan</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/25 transition-all">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 bg-gradient-to-b from-blue-50/50 via-slate-50 to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text Left -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-100/80 border border-blue-200 text-blue-700 text-xs font-bold tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Ongkir Antar-Jemput GRATIS 100%</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-tight tracking-tight">
                        Layanan Laundry <span class="bg-gradient-to-r from-blue-600 via-blue-500 to-sky-600 bg-clip-text text-transparent">Antar-Jemput</span> Tanpa Ribet
                    </h1>

                    <p class="text-lg text-slate-600 max-w-2xl font-normal leading-relaxed mx-auto lg:mx-0">
                        Harga dihitung jelas <strong class="font-extrabold text-blue-600">per buah/item</strong>, bukan kiloan. Tanpa perlu menimbang pakaian, pesan dari rumah, kurir kami langsung jemput & antar kembali!
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-500/30 transition-all text-center">
                            Pesan Laundry Sekarang
                        </a>
                        <a href="#harga" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 shadow-xs transition-all text-center">
                            Lihat Daftar Harga
                        </a>
                    </div>
                </div>

                <!-- Right Promo Image -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl overflow-hidden">
                        <img src="{{ asset('images/logo-welcome.png') }}" alt="Washly Laundry" class="w-full h-full object-contain">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Catalog Grid Section -->
    <section id="harga" class="py-16 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900">Daftar Harga Laundry Per Buah</h2>
                <p class="text-slate-500 mt-2 text-sm">Transparan dan pasti. Tanpa repot timbang kilogram.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($services as $service)
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:shadow-lg transition-all hover:-translate-y-1 group">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            👕
                        </div>
                        <h3 class="font-bold text-lg text-slate-800">{{ $service->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1 min-h-[32px]">{{ $service->description }}</p>
                        <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-medium">Harga / item</span>
                            <span class="text-xl font-extrabold text-blue-700">Rp{{ number_format($service->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section id="alur-pemesanan" class="py-16 bg-slate-100/60 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900">Cara Pemesanan</h2>
                <p class="text-slate-500 mt-2 text-sm">Proses terintegrasi dari Customer, Pembayaran, Admin, hingga Driver Pickup & Delivery.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mx-auto mb-3">1</div>
                    <h4 class="font-bold text-sm text-slate-800">Buat Order</h4>
                    <p class="text-xs text-slate-500 mt-1">Pilih pakaian & jadwal jemput</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mx-auto mb-3">2</div>
                    <h4 class="font-bold text-sm text-slate-800">Pembayaran</h4>
                    <p class="text-xs text-slate-500 mt-1">Simulasi pembayaran instan</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mx-auto mb-3">3</div>
                    <h4 class="font-bold text-sm text-slate-800">Driver Pickup</h4>
                    <p class="text-xs text-slate-500 mt-1">Penjemputan + bukti foto</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mx-auto mb-3">4</div>
                    <h4 class="font-bold text-sm text-slate-800">Outlet Processing</h4>
                    <p class="text-xs text-slate-500 mt-1">Cuci, kering & setrika rapi</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mx-auto mb-3">5</div>
                    <h4 class="font-bold text-sm text-slate-800">Driver Delivery</h4>
                    <p class="text-xs text-slate-500 mt-1">Pengantaran + bukti foto</p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
