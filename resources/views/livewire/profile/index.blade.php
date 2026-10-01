<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Pengaturan Profil</h2>

        @if (session()->has('profile_message'))
            <div class="p-4 mb-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-medium">
                {{ session('profile_message') }}
            </div>
        @endif

        <form wire:submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800">
                    @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase">Email (Readonly)</label>
                    <input type="email" wire:model="email" disabled class="mt-1 block w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-medium text-slate-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase">Nomor HP / WA</label>
                    <input type="text" wire:model="phone" class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800">
                    @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase">Alamat Utama</label>
                <textarea wire:model="address" rows="3" class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800"></textarea>
                @error('address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Password Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Ubah Password</h2>

        @if (session()->has('password_message'))
            <div class="p-4 mb-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-medium">
                {{ session('password_message') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePassword" class="space-y-4 max-w-md">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase">Password Saat Ini</label>
                <input type="password" wire:model="current_password" class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                @error('current_password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase">Password Baru</label>
                <input type="password" wire:model="new_password" class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                @error('new_password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase">Konfirmasi Password Baru</label>
                <input type="password" wire:model="new_password_confirmation" class="mt-1 block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
