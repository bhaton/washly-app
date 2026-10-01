<div class="min-h-screen lg:h-screen lg:overflow-hidden grid lg:grid-cols-5">

    <!-- Left: Brand Panel -->
    <div class="hidden lg:flex lg:col-span-2 bg-blue-700 px-8 py-8 flex-col justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 bg-white rounded-2xl px-4 py-2.5 self-start">
            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
            </div>
            <div class="w-fit">
                <span class="block font-extrabold text-xl tracking-tight text-blue-700">WASHLY</span>
                <span class="block w-full text-center text-[11px] font-semibold text-blue-500 tracking-wider uppercase">Laundry</span>
            </div>
        </a>

        <div class="my-6">
            <h2 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
                Laundry Antar-Jemput<br>Tanpa Perlu Menimbang
            </h2>
            <p class="mt-3 text-blue-100 text-sm max-w-sm leading-relaxed">
                Harga jelas per buah/item, kurir kami jemput dan antar kembali ke rumah Anda.
            </p>

            <div class="mt-7 space-y-3.5">
                <div class="flex items-start gap-3 text-white">
                    <span class="shrink-0 w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold">✓</span>
                    <div>
                        <p class="font-bold text-sm">Ongkir Antar-Jemput Gratis</p>
                        <p class="text-xs text-blue-100">Berlaku untuk seluruh area layanan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-white">
                    <span class="shrink-0 w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold">✓</span>
                    <div>
                        <p class="font-bold text-sm">Harga Per Buah, Bukan Kiloan</p>
                        <p class="text-xs text-blue-100">Rincian biaya terlihat sebelum bayar</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-white">
                    <span class="shrink-0 w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold">✓</span>
                    <div>
                        <p class="font-bold text-sm">Status Pesanan Real-time</p>
                        <p class="text-xs text-blue-100">Pantau proses jemput hingga diantar</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-blue-200">© {{ date('Y') }} Washly Laundry UMKM</p>
    </div>

    <!-- Right: Form Panel -->
    <div class="lg:col-span-3 flex items-center justify-center bg-slate-50 px-4 sm:px-8 py-10 lg:overflow-y-auto">
        <div class="w-full max-w-sm">

            <div class="lg:hidden flex items-center gap-3 justify-center mb-6">
                <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                </div>
                <div class="w-fit">
                    <span class="block font-extrabold text-lg tracking-tight text-blue-700">WASHLY</span>
                    <span class="block w-full text-center text-[11px] font-semibold text-blue-500 tracking-wider uppercase">Laundry</span>
                </div>
            </div>

            <div class="bg-white p-7 rounded-3xl shadow-lg border border-slate-100">
                <div class="text-center">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke Washly</h2>
                    <p class="mt-1.5 text-xs text-slate-500">Masuk untuk melanjutkan aktivitas laundry Anda</p>
                </div>

                <form class="mt-6 space-y-4" wire:submit.prevent="login">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700">Email Address</label>
                        <input id="email" wire:model="email" type="email" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="nama@email.com">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                        <input id="password" wire:model="password" type="password" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="••••••••">
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember" wire:model="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-xs text-slate-600">Ingat saya</label>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Masuk Sekarang
                    </button>
                </form>

                <div class="pt-5 border-t border-slate-100 space-y-3">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">Akses Cepat</p>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="loginAs('admin')" class="py-2.5 px-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold transition-all text-center">
                            🔑 Admin
                        </button>
                        <button type="button" wire:click="loginAs('driver')" class="py-2.5 px-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-extrabold transition-all text-center">
                            🛵 Driver
                        </button>
                        <button type="button" wire:click="loginAs('customer')" class="py-2.5 px-2 bg-blue-400 hover:bg-blue-500 text-white rounded-xl text-xs font-extrabold transition-all text-center">
                            👤 Customer
                        </button>
                    </div>

                    <div class="text-center">
                        <span class="text-[11px] text-slate-400 font-medium">Isi form otomatis:</span>
                        <div class="flex justify-center space-x-2 mt-0.5">
                            <button type="button" wire:click="fillDemo('admin')" class="text-[11px] text-blue-700 font-bold hover:underline">Admin</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" wire:click="fillDemo('driver')" class="text-[11px] text-blue-700 font-bold hover:underline">Driver</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" wire:click="fillDemo('customer')" class="text-[11px] text-blue-700 font-bold hover:underline">Customer</button>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-1">
                    <p class="text-xs text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">Daftar sekarang</a></p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-5">
                <a href="{{ route('home') }}" class="font-semibold text-blue-600 hover:underline">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
</div>
