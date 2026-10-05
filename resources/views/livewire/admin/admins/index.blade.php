<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Manajemen Akun Admin</h1>
            <p class="text-sm text-slate-400">Kelola akun administrator pengelola sistem Washly Laundry.</p>
        </div>
        <button wire:click="openCreateModal" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            + Tambah Admin Baru
        </button>
    </div>

    @if(session()->has('message'))
        <div class="p-4 bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 rounded-2xl text-xs font-bold flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white font-bold ml-4">&times;</button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-rose-950/60 border border-rose-800/80 text-rose-300 rounded-2xl text-xs font-bold flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white font-bold ml-4">&times;</button>
        </div>
    @endif

    <div class="bg-slate-900/90 rounded-3xl p-4 sm:p-6 border border-slate-800 shadow-xl">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama admin, email, atau HP..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-4">Nama Admin</th>
                        <th class="py-4 px-4">Kontak</th>
                        <th class="py-4 px-4">Role</th>
                        <th class="py-4 px-4">Terdaftar</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shadow-md shadow-purple-500/20">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-xs">{{ $admin->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-300">
                                {{ $admin->phone ?? '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30">
                                    ● ADMINISTRATOR
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-500 font-mono">
                                {{ $admin->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <button wire:click="openEditModal({{ $admin->id }})" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-lg transition-colors border border-slate-700">
                                    Edit
                                </button>
                                @if(Auth::id() !== $admin->id)
                                    <button wire:click="deleteAdmin({{ $admin->id }})" wire:confirm="Apakah Anda yakin ingin menghapus akun admin ini?" class="px-3 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs rounded-lg transition-colors border border-rose-500/30">
                                        Hapus
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-500 italic px-2">Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-sm">Tidak ada akun admin ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-950/80 border-t border-slate-800">
            {{ $admins->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-800 text-slate-100">
                <h3 class="font-extrabold text-white text-lg">
                    {{ $editingAdminId ? 'Edit Akun Admin' : 'Tambah Admin Baru' }}
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Admin</label>
                        <input type="text" wire:model="name" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('email') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nomor Telepon / WA (Opsional)</label>
                        <input type="text" wire:model="phone" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('phone') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Alamat (Opsional)</label>
                        <textarea wire:model="address" rows="2" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('address') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password {{ $editingAdminId ? '(Opsional)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('password') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Batal
                    </button>
                    <button wire:click="saveAdmin" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg">
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
