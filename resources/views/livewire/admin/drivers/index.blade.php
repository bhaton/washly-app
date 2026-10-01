<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Driver Lapangan</h1>
            <p class="text-sm text-slate-500">Kelola akun driver pickup & delivery, status keaktifan, dan beban kerja.</p>
        </div>
        <button wire:click="openCreateModal" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition-all">
            + Tambah Driver Baru
        </button>
    </div>

    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-xs">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama driver, email, atau HP..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-4 px-4">Nama Driver</th>
                        <th class="py-4 px-4">Kontak</th>
                        <th class="py-4 px-4">Status Active</th>
                        <th class="py-4 px-4">Total Tugas</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($drivers as $driver)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-xs">{{ $driver->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $driver->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-700">
                                {{ $driver->phone }}
                            </td>
                            <td class="py-4 px-4">
                                <button wire:click="toggleStatus({{ $driver->id }})" class="px-3 py-1 rounded-full text-xs font-bold transition-all
                                    {{ $driver->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}">
                                    {{ $driver->is_active ? '● AKTIF' : '○ NON-AKTIF' }}
                                </button>
                            </td>
                            <td class="py-4 px-4 text-xs space-x-1">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded-md">{{ $driver->pickup_tasks_count }} Pickups</span>
                                <span class="px-2 py-0.5 bg-violet-50 text-violet-700 font-bold rounded-md">{{ $driver->delivery_tasks_count }} Deliveries</span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button wire:click="openEditModal({{ $driver->id }})" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">Tidak ada driver ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200">
            {{ $drivers->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="font-extrabold text-slate-900 text-lg">
                    {{ $editingDriverId ? 'Edit Driver' : 'Tambah Driver Baru' }}
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Driver</label>
                        <input type="text" wire:model="name" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon / WA</label>
                        <input type="text" wire:model="phone" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Driver</label>
                        <textarea wire:model="address" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm"></textarea>
                        @error('address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password {{ $editingDriverId ? '(Opsional)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                        @error('password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-xs font-bold text-slate-700">Driver Aktif (Dapat Diberikan Tugas)</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                        Batal
                    </button>
                    <button wire:click="saveDriver" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md">
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
