<div class="min-h-screen lg:h-screen lg:overflow-hidden grid lg:grid-cols-5 bg-slate-950">

    <!-- Left: Brand Panel -->
    <div class="hidden lg:flex lg:col-span-2 bg-gradient-to-br from-slate-900 via-slate-900 to-blue-950 border-r border-slate-800 px-8 py-8 flex-col justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 bg-slate-900/90 border border-slate-800 backdrop-blur-xl rounded-2xl px-4 py-2.5 self-start shadow-xl">
            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-slate-950 p-1">
                <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
            </div>
            <div class="w-fit">
                <span class="block font-extrabold text-xl tracking-tight bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">WASHLY</span>
                <span class="block w-full text-center text-[10px] font-bold text-cyan-400 tracking-widest uppercase">Laundry</span>
            </div>
        </a>

        <div class="my-6">
            <h2 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
                Layanan Laundry<br><span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Kiloan & Per Item</span>
            </h2>
            <p class="mt-3 text-slate-300 text-sm max-w-sm leading-relaxed">
                Pilih paket ekonomis atau premium, reguler atau ekspres, kurir kami jemput dan antar kembali ke rumah Anda.
            </p>

            <div class="mt-7 space-y-3.5">
                <div class="flex items-start gap-3 text-slate-200">
                    <span class="shrink-0 w-2 h-2 rounded-full bg-cyan-400 mt-2"></span>
                    <div>
                        <p class="font-bold text-sm text-slate-100">Ongkir Antar-Jemput Gratis</p>
                        <p class="text-xs text-slate-400">Berlaku untuk seluruh area layanan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-slate-200">
                    <span class="shrink-0 w-2 h-2 rounded-full bg-cyan-400 mt-2"></span>
                    <div>
                        <p class="font-bold text-sm text-slate-100">Estimasi Otomatis & Transparan</p>
                        <p class="text-xs text-slate-400">Perhitungan berat & harga transparan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-slate-200">
                    <span class="shrink-0 w-2 h-2 rounded-full bg-cyan-400 mt-2"></span>
                    <div>
                        <p class="font-bold text-sm text-slate-100">Status Pesanan Real-time</p>
                        <p class="text-xs text-slate-400">Pantau proses jemput hingga diantar</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-500">© {{ date('Y') }} Washly Laundry</p>
    </div>

    <!-- Right: Form Panel -->
    <div class="lg:col-span-3 flex items-center justify-center bg-slate-950 px-4 sm:px-8 py-10 lg:overflow-y-auto">
        <div class="w-full max-w-sm">

            <div class="lg:hidden flex items-center gap-3 justify-center mb-6">
                <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-slate-900 p-1 border border-slate-800">
                    <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                </div>
                <div class="w-fit">
                    <span class="block font-extrabold text-lg tracking-tight bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">WASHLY</span>
                    <span class="block w-full text-center text-[10px] font-bold text-cyan-400 tracking-widest uppercase">Laundry</span>
                </div>
            </div>

            <div class="bg-slate-900/90 p-7 rounded-3xl shadow-2xl border border-slate-800 backdrop-blur-xl">
                <div class="text-center">
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">Masuk ke Washly</h2>
                    <p class="mt-1.5 text-xs text-slate-400">Masuk untuk melanjutkan aktivitas laundry Anda</p>
                </div>

                <form class="mt-6 space-y-4" wire:submit.prevent="login">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-300">Email / Nomor HP</label>
                        <input id="email" wire:model="email" type="text" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="nama@email.com / 08123456789">
                        @error('email') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                        <input id="password" wire:model="password" type="password" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                        @error('password') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember" wire:model="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 bg-slate-950 border-slate-800 rounded">
                        <label for="remember" class="ml-2 block text-xs text-slate-400">Ingat saya</label>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-lg shadow-blue-500/25 transition-all">
                        Masuk Sekarang
                    </button>
                </form>

                <div class="text-center pt-5 border-t border-slate-800 mt-4">
                    <p class="text-xs text-slate-400">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-cyan-400 hover:underline">Daftar sekarang</a></p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-500 mt-5">
                <a href="{{ route('home') }}" class="font-semibold text-cyan-400 hover:underline">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
</div>
