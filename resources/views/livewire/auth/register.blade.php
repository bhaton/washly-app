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
                Daftar Sekarang,<br><span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Laundry Tak Bikin Repot</span>
            </h2>
            <p class="mt-3 text-slate-300 text-sm max-w-sm leading-relaxed">
                Pilih layanan, tentukan jadwal jemput, lalu sisakan sisanya untuk kami.
            </p>

            <div class="mt-7 space-y-3.5">
                <div class="flex items-start gap-3 text-slate-200">
                    <span class="shrink-0 w-2 h-2 rounded-full bg-cyan-400 mt-2"></span>
                    <div>
                        <p class="font-bold text-sm text-slate-100">Gratis Ongkir</p>
                        <p class="text-xs text-slate-400">Jemput dan antar tanpa biaya tambahan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-slate-200">
                    <span class="shrink-0 w-2 h-2 rounded-full bg-cyan-400 mt-2"></span>
                    <div>
                        <p class="font-bold text-sm text-slate-100">Harga Transparan</p>
                        <p class="text-xs text-slate-400">Lihat rincian per item sebelum konfirmasi</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-slate-200">
                    <span class="shrink-0 w-2 h-2 rounded-full bg-cyan-400 mt-2"></span>
                    <div>
                        <p class="font-bold text-sm text-slate-100">Bisa Pantau Real-Time</p>
                        <p class="text-xs text-slate-400">Status penjemputan sampai antar terlihat jelas</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-500">© {{ date('Y') }} Washly Laundry UMKM</p>
    </div>

    <!-- Right: Form Panel -->
    <div class="lg:col-span-3 flex items-center justify-center bg-slate-950 px-4 sm:px-8 py-10 lg:overflow-y-auto">
        <div class="w-full max-w-2xl">

            <div class="lg:hidden flex items-center gap-3 justify-center mb-6">
                <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-slate-900 p-1 border border-slate-800">
                    <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                </div>
                <div class="w-fit">
                    <span class="block font-extrabold text-lg tracking-tight bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">WASHLY</span>
                    <span class="block w-full text-center text-[10px] font-bold text-cyan-400 tracking-widest uppercase">Laundry</span>
                </div>
            </div>

            <div class="bg-slate-900/90 p-6 sm:p-7 rounded-3xl shadow-2xl border border-slate-800 backdrop-blur-xl">
                <div class="text-center space-y-1">
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">Daftar Akun Pelanggan (Customer)</h2>
                    <p class="text-xs text-slate-400">Mulai pesan laundry antar-jemput hari ini</p>
                    <div class="mt-2 p-3 bg-blue-500/10 rounded-xl border border-blue-500/30 text-[11px] text-cyan-300 text-left font-medium">
                        <strong>Informasi:</strong> Pendaftaran publik ini khusus untuk akun <strong>Pelanggan (Customer)</strong>. Untuk akun Admin & Driver dibuat dan dikelola oleh Administrator via Dashboard Operasional.
                    </div>
                </div>

                <form class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3" wire:submit.prevent="register">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-300">Nama Lengkap</label>
                        <input id="name" wire:model="name" type="text" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Budi Santoso">
                        @error('name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-300">Email Address</label>
                        <input id="email" wire:model="email" type="email" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="budi@email.com">
                        @error('email') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-300">Nomor Telepon / WhatsApp</label>
                        <input id="phone" wire:model="phone" type="text" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="081234567890">
                        @error('phone') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-300">Alamat Jemput/Antar</label>
                        <textarea id="address" wire:model="address" rows="2" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Jl. Anggrek No. 123, Kebayoran Baru..."></textarea>
                        @error('address') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                        <input id="password" wire:model="password" type="password" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                        @error('password') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300">Konfirmasi Password</label>
                        <input id="password_confirmation" wire:model="password_confirmation" type="password" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="••••••••">
                    </div>

                    <div class="sm:col-span-2 mt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-lg shadow-blue-500/25 transition-all">
                            Daftar Akun Baru
                        </button>
                    </div>
                </form>

                <div class="text-center pt-4 border-t border-slate-800 mt-4">
                    <p class="text-xs text-slate-400">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-cyan-400 hover:underline">Masuk di sini</a></p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-500 mt-5">
                <a href="{{ route('home') }}" class="font-semibold text-cyan-400 hover:underline">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
</div>
