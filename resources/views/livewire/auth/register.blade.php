<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-lg shadow-indigo-500/30">
                W
            </div>
            <h2 class="mt-4 text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Customer</h2>
            <p class="mt-2 text-sm text-slate-500">Mulai pesan laundry antar-jemput hari ini</p>
        </div>

        <form class="mt-8 space-y-4" wire:submit.prevent="register">
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                <input id="name" wire:model="name" type="text" required class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Budi Santoso">
                @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                <input id="email" wire:model="email" type="email" required class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="budi@email.com">
                @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                <input id="phone" wire:model="phone" type="text" required class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="081234567890">
                @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-slate-700">Alamat Lengkap (Lokasi Jemput/Antar)</label>
                <textarea id="address" wire:model="address" rows="2" required class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Jl. Anggrek No. 123, RT 01/RW 02, Kebayoran Baru..."></textarea>
                @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                <input id="password" wire:model="password" type="password" required class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="••••••••">
                @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                <input id="password_confirmation" wire:model="password_confirmation" type="password" required class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="••••••••">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/25 transition-all">
                    Daftar Akun Baru
                </button>
            </div>
        </form>

        <div class="text-center pt-2">
            <p class="text-xs text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">Masuk di sini</a></p>
        </div>
    </div>
</div>
