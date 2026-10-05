<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-2xl backdrop-blur-xl">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Kelola Pop-up Promosi & Diskon</h1>
            <p class="text-sm text-slate-400 mt-1">Atur pengumuman promo, diskon, dan banner pop-up yang tampil untuk pelanggan (Customer).</p>
        </div>
        <button wire:click="create" class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 shadow-lg shadow-blue-500/25 transition-all hover:scale-105">
            + Tambah Promosi Baru
        </button>
    </div>

    <!-- Alert Notification -->
    @if (session()->has('message'))
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 text-sm font-medium flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Promotions Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($promotions as $promo)
            <div class="p-6 rounded-3xl bg-slate-900/90 border {{ $promo->is_active ? 'border-blue-500/40' : 'border-slate-800' }} shadow-xl space-y-4 relative flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $promo->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $promo->badge ?? 'PROMO' }}
                        </span>
                        <button wire:click="toggleActive({{ $promo->id }})" class="px-3 py-1 rounded-xl text-xs font-bold transition-all {{ $promo->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30 hover:bg-rose-500/20' }}">
                            {{ $promo->is_active ? 'Aktif (Pop-up Tampil)' : 'Non-Aktif' }}
                        </button>
                    </div>

                    <!-- Poster Image Preview in Card -->
                    @if($promo->image)
                        <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 h-36">
                            <img src="{{ asset($promo->image) }}" alt="{{ $promo->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <h3 class="text-lg font-bold text-white leading-snug">{{ $promo->title }}</h3>
                    <p class="text-xs text-slate-300 leading-relaxed line-clamp-3">{{ $promo->description }}</p>

                    @if($promo->promo_code)
                        <div class="p-2.5 rounded-xl bg-slate-950/80 border border-dashed border-blue-500/40 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Kode Kupon:</span>
                            <span class="font-mono font-bold text-sm text-cyan-400 tracking-wider">{{ $promo->promo_code }}</span>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2">
                    <button wire:click="edit({{ $promo->id }})" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition-all">
                        Edit
                    </button>
                    <button wire:click="delete({{ $promo->id }})" wire:confirm="Apakah Anda yakin ingin menghapus promosi ini?" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition-all">
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-3xl bg-slate-900/50 border border-slate-800">
                <p class="text-slate-400 text-sm">Belum ada promo yang dibuat. Klik tombol di atas untuk menambahkan promosi baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md overflow-y-auto">
            <div class="w-full max-w-lg rounded-3xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-6 my-8">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-white">{{ $editingId ? 'Edit Promosi' : 'Tambah Promosi Baru' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-white font-bold p-1">
                        X
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Judul Promo *</label>
                        <input type="text" wire:model="title" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Contoh: Diskon Sumpah Pemuda 25%">
                        @error('title') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Foto / Banner Poster Promo</label>
                        <input type="file" wire:model="imageUpload" accept="image/*" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                        @error('imageUpload') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror

                        <!-- Preview Poster Image -->
                        <div class="mt-3">
                            @if ($imageUpload)
                                <p class="text-[10px] text-slate-400 mb-1 font-semibold">Preview Poster Baru:</p>
                                <div class="w-full h-40 rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                                    <img src="{{ $imageUpload->temporaryUrl() }}" class="w-full h-full object-cover">
                                </div>
                            @elseif($existingImage)
                                <p class="text-[10px] text-slate-400 mb-1 font-semibold">Poster Saat Ini:</p>
                                <div class="w-full h-40 rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                                    <img src="{{ asset($existingImage) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Badge / Label Singkat</label>
                        <input type="text" wire:model="badge" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Contoh: DISKON 25% atau PROMO KILOAN">
                        @error('badge') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Kupon / Promo Code (Opsional)</label>
                        <input type="text" wire:model="promo_code" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white font-mono uppercase focus:outline-none focus:border-blue-500" placeholder="Contoh: WASHLY25">
                        @error('promo_code') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi & Syarat Ketentuan Promo *</label>
                        <textarea wire:model="description" rows="4" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Jelaskan detail potongan harga, jenis layanan yang berlaku, dan minimal transaksi..."></textarea>
                        @error('description') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-0">
                        <label for="is_active" class="text-sm font-semibold text-slate-300">Tampilkan Pop-up Ini Sekarang (Aktif)</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/25">
                            Simpan Promosi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
