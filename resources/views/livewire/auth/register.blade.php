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
                Daftar Sekarang,<br>Laundry Tak Bikin Repot
            </h2>
            <p class="mt-3 text-blue-100 text-sm max-w-sm leading-relaxed">
                Pilih layanan, tentukan jadwal jemput, lalu sisakan sisanya untuk kami.
            </p>

            <div class="mt-7 space-y-3.5">
                <div class="flex items-start gap-3 text-white">
                    <span class="shrink-0 w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold">✓</span>
                    <div>
                        <p class="font-bold text-sm">Gratis Ongkir</p>
                        <p class="text-xs text-blue-100">Jemput dan antar tanpa biaya tambahan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-white">
                    <span class="shrink-0 w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold">✓</span>
                    <div>
                        <p class="font-bold text-sm">Harga transparan</p>
                        <p class="text-xs text-blue-100">Lihat rincian per item sebelum konfirmasi</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-white">
                    <span class="shrink-0 w-6 h-6 rounded-lg bg-white/15 flex items-center justify-center text-xs font-bold">✓</span>
                    <div>
                        <p class="font-bold text-sm">Bisa pantau sendiri</p>
                        <p class="text-xs text-blue-100">Status penjemputan sampai antar terlihat jelas</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-blue-200">© {{ date('Y') }} Washly Laundry UMKM</p>
    </div>

    <!-- Right: Form Panel -->
    <div class="lg:col-span-3 flex items-center justify-center bg-slate-50 px-4 sm:px-8 py-10 lg:overflow-y-auto">
        <div class="w-full max-w-2xl">

            <div class="lg:hidden flex items-center gap-3 justify-center mb-6">
                <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Washly Logo" class="w-full h-full object-contain">
                </div>
                <div class="w-fit">
                    <span class="block font-extrabold text-lg tracking-tight text-blue-700">WASHLY</span>
                    <span class="block w-full text-center text-[11px] font-semibold text-blue-500 tracking-wider uppercase">Laundry</span>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-lg border border-slate-100">
                <div class="text-center">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Customer</h2>
                    <p class="mt-1.5 text-xs text-slate-500">Mulai pesan laundry antar-jemput hari ini</p>
                </div>

                <form class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3" wire:submit.prevent="register">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700">Nama Lengkap</label>
                        <input id="name" wire:model="name" type="text" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="Budi Santoso">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700">Email Address</label>
                        <input id="email" wire:model="email" type="email" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="budi@email.com">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                        <input id="phone" wire:model="phone" type="text" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="081234567890">
                        @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-700">Alamat Jemput/Antar</label>
                        <textarea id="address" wire:model="address" rows="2" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="Jl. Anggrek No. 123, Kebayoran Baru..."></textarea>
                        @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                        <input id="password" wire:model="password" type="password" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="••••••••">
                        @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Konfirmasi Password</label>
                        <input id="password_confirmation" wire:model="password_confirmation" type="password" required class="mt-1.5 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm" placeholder="••••••••">
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all">
                            Daftar Akun Baru
                        </button>
                    </div>
                </form>

                <div class="text-center pt-1">
                    <p class="text-xs text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">Masuk di sini</a></p>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-5">
                <a href="{{ route('home') }}" class="font-semibold text-blue-600 hover:underline">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
</div>
