<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Katalog Layanan & Harga Per Item</h1>
            <p class="text-sm text-slate-400">Kelola item laundry (harga per buah, bukan kiloan), deskripsi, dan status aktif.</p>
        </div>
        <button wire:click="openCreateModal" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-sm rounded-xl shadow-lg transition-all">
            + Tambah Item Baru
        </button>
    </div>

    <div class="bg-slate-900/90 rounded-3xl p-4 sm:p-6 border border-slate-800 shadow-xl">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama item atau deskripsi..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between space-y-4 text-slate-100">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-cyan-400 border border-blue-500/30 flex items-center justify-center font-bold text-xs uppercase tracking-wider">
                            {{ substr($service->name, 0, 2) }}
                        </div>
                        <button wire:click="toggleStatus({{ $service->id }})" class="px-2.5 py-0.5 rounded-full text-xs font-bold transition-all
                            {{ $service->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                            {{ $service->is_active ? '● AKTIF' : '○ NON-AKTIF' }}
                        </button>
                    </div>

                    <h3 class="font-extrabold text-white text-lg">{{ $service->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1 min-h-[36px]">{{ $service->description }}</p>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block">Harga / Piece</span>
                        <span class="text-xl font-black text-cyan-400">Rp{{ number_format($service->price, 0, ',', '.') }}</span>
                    </div>

                    <button wire:click="openEditModal({{ $service->id }})" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition-colors border border-slate-700">
                        Edit
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-500 text-sm">Belum ada item laundry.</div>
        @endforelse
    </div>

    <div class="p-4">
        {{ $services->links() }}
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-800 text-slate-100">
                <h3 class="font-extrabold text-white text-lg">
                    {{ $editingServiceId ? 'Edit Item Laundry' : 'Tambah Item Laundry Baru' }}
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Item</label>
                        <input type="text" wire:model="name" placeholder="Contoh: Kemeja, Kaos, Jeans..." class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Harga Per Piece / Buah (Rp)</label>
                        <input type="number" wire:model="price" placeholder="8000" class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('price') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Deskripsi Layanan</label>
                        <textarea wire:model="description" rows="2" placeholder="Layanan cuci kemeja formal & setrika halus..." class="w-full px-3.5 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="rounded text-blue-600 bg-slate-950 border-slate-800 focus:ring-blue-500">
                        <label for="is_active" class="text-xs font-bold text-slate-300">Tampilkan di Katalog (Aktif)</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Batal
                    </button>
                    <button wire:click="saveService" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg">
                        Simpan Item
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
