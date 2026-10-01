<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-lg shadow-indigo-500/30">
                W
            </div>
            <h2 class="mt-4 text-3xl font-extrabold text-slate-900 tracking-tight">Masuk ke Washly</h2>
            <p class="mt-2 text-sm text-slate-500">Pilih akun Anda atau gunakan tombol instan di bawah</p>
        </div>

        <form class="mt-8 space-y-6" wire:submit.prevent="login">
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                    <input id="email" wire:model="email" type="email" required class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm" placeholder="nama@email.com">
                    @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" wire:model="password" type="password" required class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm" placeholder="••••••••">
                    @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" wire:model="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-slate-600">Ingat saya</label>
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-500/25 transition-all">
                    Masuk Sekarang
                </button>
            </div>
        </form>

        <!-- Demo Accounts Quick Fill & Instant Login Buttons -->
        <div class="pt-6 border-t border-slate-100 space-y-3">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center">⚡ 1-Click Login Akun Demo:</p>

            <div class="grid grid-cols-3 gap-2">
                <button type="button" wire:click="loginAs('admin')" class="py-2.5 px-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition-all text-center">
                    🔑 Admin
                </button>
                <button type="button" wire:click="loginAs('driver')" class="py-2.5 px-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition-all text-center">
                    🛵 Driver
                </button>
                <button type="button" wire:click="loginAs('customer')" class="py-2.5 px-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition-all text-center">
                    👤 Customer
                </button>
            </div>

            <div class="pt-2 text-center">
                <span class="text-[11px] text-slate-400 font-medium">Atau isi form secara otomatis:</span>
                <div class="flex justify-center space-x-2 mt-1">
                    <button type="button" wire:click="fillDemo('admin')" class="text-[11px] text-purple-700 font-bold hover:underline">Isi Admin</button>
                    <span class="text-slate-300">•</span>
                    <button type="button" wire:click="fillDemo('driver')" class="text-[11px] text-blue-700 font-bold hover:underline">Isi Driver</button>
                    <span class="text-slate-300">•</span>
                    <button type="button" wire:click="fillDemo('customer')" class="text-[11px] text-emerald-700 font-bold hover:underline">Isi Customer</button>
                </div>
            </div>
        </div>

        <div class="text-center pt-2">
            <p class="text-xs text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline">Daftar sekarang</a></p>
        </div>
    </div>
</div>
