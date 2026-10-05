<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Manajemen Driver Lapangan</h1>
            <p class="text-sm text-slate-400">Kelola akun driver pickup & delivery, status keaktifan, dan beban kerja.</p>
        </div>
        <button wire:click="openCreateModal" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            + Tambah Driver Baru
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
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama driver, email, atau HP..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-4">Nama Driver</th>
                        <th class="py-4 px-4">Kontak</th>
                        <th class="py-4 px-4">Status Active</th>
                        <th class="py-4 px-4">Total Tugas</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($drivers as $driver)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shadow-md shadow-blue-500/20">
                                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-xs">{{ $driver->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $driver->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-300">
                                {{ $driver->phone }}
                            </td>
                            <td class="py-4 px-4">
                                <button wire:click="toggleStatus({{ $driver->id }})" class="px-3 py-1 rounded-full text-xs font-bold transition-all
                                    {{ $driver->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                                    {{ $driver->is_active ? '● AKTIF' : '○ NON-AKTIF' }}
                                </button>
                            </td>
                            <td class="py-4 px-4 text-xs space-x-1">
                                <span class="px-2 py-0.5 bg-blue-500/10 text-cyan-400 font-bold rounded-md border border-blue-500/30">{{ $driver->pickup_tasks_count }} Pickups</span>
                                <span class="px-2 py-0.5 bg-sky-500/10 text-sky-400 font-bold rounded-md border border-sky-500/30">{{ $driver->delivery_tasks_count }} Deliveries</span>
                            </td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <button wire:click="openEditModal({{ $driver->id }})" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-lg transition-colors border border-slate-700">
                                    Edit
                                </button>
                                <button wire:click="deleteDriver({{ $driver->id }})" wire:confirm="Apakah Anda yakin ingin menghapus akun driver ini?" class="px-3 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs rounded-lg transition-colors border border-rose-500/30">
                                    Hapus
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-sm">Tidak ada driver ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-950/80 border-t border-slate-800">
            {{ $drivers->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-800 text-slate-100">
                <h3 class="font-extrabold text-white text-lg">
                    {{ $editingDriverId ? 'Edit Driver' : 'Tambah Driver Baru' }}
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Driver</label>
                        <input type="text" wire:model="name" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('email') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nomor Telepon / WA</label>
                        <input type="text" wire:model="phone" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('phone') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Alamat Driver</label>
                        <textarea wire:model="address" rows="2" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('address') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password {{ $editingDriverId ? '(Opsional)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('password') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="rounded text-blue-600 bg-slate-950 border-slate-800 focus:ring-blue-500">
                        <label for="is_active" class="text-xs font-bold text-slate-300">Driver Aktif (Dapat Diberikan Tugas)</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Batal
                    </button>
                    <button wire:click="saveDriver" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg">
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
