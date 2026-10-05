<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Washly Laundry - Layanan Antar Jemput Laundry Kiloan & Per Item</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS (Animate On Scroll) Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        /* Smooth Scroll Behavior */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased min-h-screen flex flex-col selection:bg-blue-600 selection:text-white overflow-x-hidden">

    <!-- Header / Navbar -->
    <nav class="bg-slate-900/90 backdrop-blur-xl sticky top-0 z-30 border-b border-slate-800/80 shadow-2xl shadow-slate-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl overflow-hidden shadow-lg shadow-blue-500/30 ring-1 ring-blue-500/40 shrink-0 bg-slate-950 p-1">
                        <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="w-fit">
                        <span class="block font-black text-2xl tracking-tight bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">WASHLY</span>
                        <span class="block w-full text-center text-[10px] font-bold text-cyan-400 tracking-widest uppercase">Laundry</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-2">
                    <a href="#harga" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800/60 transition-all">Layanan & Harga</a>
                    <a href="#partner" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800/60 transition-all">Brand Partner</a>
                    <a href="#alur-pemesanan" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800/60 transition-all">Alur Pemesanan</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-lg shadow-blue-500/25 transition-all hover:scale-105">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-16 pb-24 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text Left -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left" data-aos="fade-right" data-aos-duration="900">
                    <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-cyan-400 text-xs font-bold tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Layanan Antar-Jemput Gratis 100%</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight">
                        Layanan Laundry <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">Kiloan & Per Item</span> Antar-Jemput
                    </h1>

                    <p class="text-lg text-slate-300 max-w-2xl font-normal leading-relaxed mx-auto lg:mx-0">
                        Hitungan transparan <strong class="font-bold text-cyan-400">Per Kilogram (Kiloan)</strong> & <strong class="font-bold text-indigo-400">Per Item</strong>. Pilih Paket Ekonomis atau Premium, dengan opsi kecepatan <strong class="font-bold text-white">Reguler (2 Hari)</strong> atau <strong class="font-bold text-amber-400">Ekspres (24 Jam)</strong>. Estimasi biaya otomatis langsung dihitung!
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-xl shadow-blue-500/30 transition-all text-center hover:scale-[1.02]">
                            Pesan Laundry Sekarang
                        </a>
                        <a href="#harga" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold text-slate-200 bg-slate-900/90 hover:bg-slate-800 border border-slate-800 shadow-lg transition-all text-center">
                            Lihat Daftar Harga
                        </a>
                    </div>
                </div>

                <!-- Right Promo Image -->
                <div class="lg:col-span-5" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="150">
                    <div class="rounded-3xl overflow-hidden shadow-2xl ring-1 ring-slate-800 bg-slate-900/80 p-2 hover:scale-[1.01] transition-transform duration-500">
                        <img src="{{ asset('images/logo-welcome.png') }}" alt="Washly Laundry" class="w-full h-full object-contain rounded-2xl">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing & Services Catalog Grid Section -->
    <section id="harga" class="py-16 bg-slate-950 scroll-mt-20 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto" data-aos="fade-up" data-aos-duration="800">
                <h2 class="text-3xl font-extrabold text-white">Daftar Layanan & Harga</h2>
                <p class="text-slate-400 mt-2 text-sm">Pilih Laundry Kiloan (Per kg) atau Laundry Per Item sesuai kebutuhan Anda.</p>
            </div>

            <!-- Laundry Kiloan Highlights -->
            <div class="p-8 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/90 to-blue-950 text-white border border-blue-500/30 shadow-2xl space-y-6" data-aos="fade-up" data-aos-duration="900" data-aos-delay="100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-6">
                    <div>
                        <span class="px-3 py-1 bg-blue-500/20 border border-blue-400/30 text-cyan-300 text-xs font-black rounded-full uppercase tracking-wider">Layanan Utama</span>
                        <h3 class="text-2xl font-black text-white mt-2">Laundry Kiloan (Per Kilogram)</h3>
                        <p class="text-xs text-slate-400 mt-1">Perhitungan transparan berdasarkan berat akhir hasil penimbangan outlet.</p>
                    </div>
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-sm rounded-2xl shadow-lg transition-all hover:scale-105">
                        Pesan Kiloan
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl bg-slate-950/80 backdrop-blur-md border border-slate-800 space-y-3 hover:border-blue-500/40 transition-all" data-aos="fade-right" data-aos-delay="200">
                        <div class="flex justify-between items-center">
                            <h4 class="font-extrabold text-lg text-white">Paket Ekonomis / Murah</h4>
                            <span class="text-xs font-bold px-2.5 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg">Hemat</span>
                        </div>
                        <p class="text-xs text-slate-400">Layanan cuci higienis + detergent standar + lipat rapi.</p>
                        <div class="pt-3 border-t border-slate-800/80 flex justify-between items-center text-xs">
                            <span class="text-slate-400">Reguler (2 Hari): <strong class="text-white text-sm">Rp8.000/kg</strong></span>
                            <span class="text-slate-400">Ekspres (24 Jam): <strong class="text-amber-400 text-sm">Rp13.000/kg</strong></span>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-950/80 backdrop-blur-md border border-slate-800 space-y-3 hover:border-amber-500/40 transition-all" data-aos="fade-left" data-aos-delay="300">
                        <div class="flex justify-between items-center">
                            <h4 class="font-extrabold text-lg text-white">Paket Premium</h4>
                            <span class="text-xs font-bold px-2.5 py-1 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-lg">Best Quality</span>
                        </div>
                        <p class="text-xs text-slate-400">Softener harum tahan lama + setrika halus + disinfektan.</p>
                        <div class="pt-3 border-t border-slate-800/80 flex justify-between items-center text-xs">
                            <span class="text-slate-400">Reguler (2 Hari): <strong class="text-white text-sm">Rp12.000/kg</strong></span>
                            <span class="text-slate-400">Ekspres (24 Jam): <strong class="text-amber-400 text-sm">Rp18.000/kg</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laundry Per Item Catalog -->
            <div class="space-y-6">
                <div class="flex justify-between items-center" data-aos="fade-up" data-aos-duration="700">
                    <h3 class="text-xl font-extrabold text-white">Laundry Per Item / Pcs</h3>
                    <span class="text-xs font-bold text-slate-400">Reguler & Ekspres Available</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($services->where('category', 'item') as $index => $service)
                        <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" 
                             data-aos="fade-up" 
                             data-aos-delay="{{ min(($index % 4) * 100 + 100, 400) }}"
                             data-aos-duration="800">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-cyan-400 border border-blue-500/30 flex items-center justify-center font-extrabold text-xs mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all uppercase tracking-wider">
                                ITEM
                            </div>
                            <h4 class="font-bold text-lg text-slate-100">{{ $service->name }}</h4>
                            <p class="text-xs text-slate-400 mt-1 min-h-[32px]">{{ $service->description }}</p>
                            <div class="mt-4 pt-4 border-t border-slate-800 flex justify-between items-center">
                                <span class="text-xs text-slate-500 font-medium">Mulai dari</span>
                                <span class="text-lg font-extrabold text-cyan-400">
                                    @if($service->price > 0)
                                        Rp{{ number_format($service->price, 0, ',', '.') }}
                                    @else
                                        Konfirmasi Admin
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 8: Brand Partner -->
    <section id="partner" class="py-16 bg-slate-900/60 text-white scroll-mt-20 border-t border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center max-w-2xl mx-auto" data-aos="fade-up" data-aos-duration="800">
                <span class="px-3.5 py-1 bg-blue-500/10 border border-blue-500/30 text-cyan-400 text-xs font-bold rounded-full uppercase tracking-widest">Dipercaya Mesin & Detergent Ternama</span>
                <h2 class="text-3xl font-black text-white mt-3">Brand Partner Kami</h2>
                <p class="text-slate-400 mt-2 text-sm">Kami menggunakan peralatan mesin modern & bahan pewangi deterjen berkualitas tinggi dari mitra ternama.</p>
            </div>

            <!-- Brand Logo Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 pt-4">
                <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col items-center justify-center space-y-3 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" data-aos="zoom-in" data-aos-delay="100">
                    <img src="{{ asset('images/brands/lg.svg') }}" alt="LG Commercial Washers" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-110 filter drop-shadow brightness-110" />
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider text-center group-hover:text-cyan-400 transition-colors">Commercial Washers</span>
                </div>

                <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col items-center justify-center space-y-3 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" data-aos="zoom-in" data-aos-delay="200">
                    <img src="{{ asset('images/brands/samsung.svg') }}" alt="Samsung Dryer Systems" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-110 filter drop-shadow brightness-110" />
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider text-center group-hover:text-cyan-400 transition-colors">Dryer Systems</span>
                </div>

                <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col items-center justify-center space-y-3 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" data-aos="zoom-in" data-aos-delay="300">
                    <img src="{{ asset('images/brands/electrolux.svg') }}" alt="Electrolux Steam Care" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-110 filter drop-shadow brightness-110" />
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider text-center group-hover:text-cyan-400 transition-colors">Steam Care</span>
                </div>

                <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col items-center justify-center space-y-3 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" data-aos="zoom-in" data-aos-delay="400">
                    <img src="{{ asset('images/brands/attack.svg') }}" alt="Attack Hygiene Detergent" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-110 filter drop-shadow brightness-110" />
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider text-center group-hover:text-cyan-400 transition-colors">Hygiene Detergent</span>
                </div>

                <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col items-center justify-center space-y-3 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" data-aos="zoom-in" data-aos-delay="500">
                    <img src="{{ asset('images/brands/molto.svg') }}" alt="Molto Softener & Fragrance" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-110 filter drop-shadow brightness-110" />
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider text-center group-hover:text-cyan-400 transition-colors">Softener & Fragrance</span>
                </div>

                <div class="p-6 bg-slate-900/90 rounded-2xl border border-slate-800 flex flex-col items-center justify-center space-y-3 hover:border-blue-500/50 hover:bg-slate-800/80 transition-all duration-300 group shadow-lg" data-aos="zoom-in" data-aos-delay="600">
                    <img src="{{ asset('images/brands/downy.svg') }}" alt="Downy Long-lasting Parfum" class="h-9 w-auto object-contain transition-transform duration-300 group-hover:scale-110 filter drop-shadow brightness-110" />
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider text-center group-hover:text-cyan-400 transition-colors">Long-lasting Parfum</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section id="alur-pemesanan" class="py-16 bg-slate-950 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up" data-aos-duration="800">
                <h2 class="text-3xl font-extrabold text-white">Alur Kerja & Pemesanan</h2>
                <p class="text-slate-400 mt-2 text-sm">Alur bisnis transparan dari penjemputan, pencucian, penimbangan, tagihan hingga pengantaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 text-center shadow-lg hover:border-blue-500/40 transition-all" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center mx-auto mb-3 shadow-md shadow-blue-500/20">1</div>
                    <h4 class="font-bold text-sm text-slate-100">Menunggu Pickup</h4>
                    <p class="text-xs text-slate-400 mt-1">Order masuk & admin menugaskan driver pickup</p>
                </div>
                <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 text-center shadow-lg hover:border-blue-500/40 transition-all" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center mx-auto mb-3 shadow-md shadow-blue-500/20">2</div>
                    <h4 class="font-bold text-sm text-slate-100">Driver Picked Up</h4>
                    <p class="text-xs text-slate-400 mt-1">Driver jemput pakaian & admin konfirmasi outlet</p>
                </div>
                <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 text-center shadow-lg hover:border-blue-500/40 transition-all" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center mx-auto mb-3 shadow-md shadow-blue-500/20">3</div>
                    <h4 class="font-bold text-sm text-slate-100">Proses & Penimbangan</h4>
                    <p class="text-xs text-slate-400 mt-1">Cuci, kering & penimbangan berat akhir</p>
                </div>
                <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 text-center shadow-lg hover:border-blue-500/40 transition-all" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center mx-auto mb-3 shadow-md shadow-blue-500/20">4</div>
                    <h4 class="font-bold text-sm text-slate-100">Tagihan & Resi</h4>
                    <p class="text-xs text-slate-400 mt-1">Admin buat tagihan & cetak resi pembayaran</p>
                </div>
                <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 text-center shadow-lg hover:border-blue-500/40 transition-all" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center mx-auto mb-3 shadow-md shadow-blue-500/20">5</div>
                    <h4 class="font-bold text-sm text-slate-100">Delivery & Bayar</h4>
                    <p class="text-xs text-slate-400 mt-1">Driver antar laundry, bawa resi & terima bayar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- AOS JS Script -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: false,
                mirror: true,
                easing: 'ease-out-cubic',
                offset: 100
            });
        });
    </script>
</body>
</html>
