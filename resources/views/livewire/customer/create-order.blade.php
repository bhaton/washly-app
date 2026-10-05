<div class="max-w-4xl mx-auto space-y-8 relative">
    <!-- Live Toast / Action Notification -->
    @if($notification)
        @php
            $bgColor = match($notification['type']) {
                'success' => 'bg-emerald-600 text-white border-emerald-700 shadow-emerald-500/20',
                'warning' => 'bg-amber-500 text-white border-amber-600 shadow-amber-500/20',
                'error'   => 'bg-rose-600 text-white border-rose-700 shadow-rose-500/20',
                'info'    => 'bg-blue-600 text-white border-blue-700 shadow-blue-500/20',
                default   => 'bg-slate-800 text-white border-slate-900 shadow-slate-500/20',
            };
        @endphp
        <div wire:key="action-toast-{{ microtime() }}" x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-10px]" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-10px]" class="fixed top-5 right-5 z-50 max-w-md p-4 rounded-2xl shadow-2xl border flex items-center justify-between space-x-3 transition-all {{ $bgColor }}">
            <div class="flex items-center space-x-3">
                <span class="text-xs sm:text-sm font-bold leading-tight">{{ $notification['message'] }}</span>
            </div>
            <button type="button" wire:click="dismissNotification" @click="show = false" class="p-1 hover:bg-white/20 rounded-lg transition-colors focus:outline-none shrink-0 text-white font-black text-sm">
                &times;
            </button>
        </div>
    @endif

    <!-- Wizard Progress Stepper -->
    <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <button type="button" @if($step > 1) wire:click="$set('step', 1)" @endif class="flex items-center space-x-2 text-left focus:outline-none cursor-pointer">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 1 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-950 text-slate-500 border border-slate-800' }}">1</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 1 ? 'text-white font-bold' : 'text-slate-500' }}">Layanan & Item</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-800 mx-2"></div>

            <button type="button" @if($step > 2) wire:click="$set('step', 2)" @endif class="flex items-center space-x-2 text-left focus:outline-none cursor-pointer">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 2 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-950 text-slate-500 border border-slate-800' }}">2</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 2 ? 'text-white font-bold' : 'text-slate-500' }}">Pickup</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-800 mx-2"></div>

            <button type="button" @if($step > 3) wire:click="$set('step', 3)" @endif class="flex items-center space-x-2 text-left focus:outline-none cursor-pointer">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 3 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-950 text-slate-500 border border-slate-800' }}">3</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 3 ? 'text-white font-bold' : 'text-slate-500' }}">Delivery</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-800 mx-2"></div>

            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center {{ $step == 4 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-950 text-slate-500 border border-slate-800' }}">4</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step == 4 ? 'text-emerald-400 font-bold' : 'text-slate-500' }}">Konfirmasi</span>
            </div>
        </div>
    </div>

    <!-- STEP 1: Select Service, Package, Speed & Items -->
    @if($step == 1)
        <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl backdrop-blur-xl space-y-8 text-slate-100">
            <div>
                <h2 class="text-2xl font-black text-white">Step 1: Pilih Layanan Laundry</h2>
                <p class="text-xs text-slate-400 mt-1">Pilih jenis layanan, paket, kecepatan, dan daftar pakaian Anda.</p>
            </div>

            @if($step1_error || session()->has('step1_error'))
                <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-2xl text-xs sm:text-sm font-extrabold flex items-center space-x-3">
                    <span>{{ $step1_error ?: session('step1_error') }}</span>
                </div>
            @endif

            <!-- 1. Service Type Selector (Kiloan vs Per Item) -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">1. Pilih Jenis Layanan Utama</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button type="button" wire:click="selectServiceType('kiloan')" class="p-5 rounded-2xl border-2 text-left transition-all relative overflow-hidden cursor-pointer {{ $service_type === 'kiloan' ? 'border-cyan-500 bg-cyan-500/10 shadow-lg ring-1 ring-cyan-500/30' : 'border-slate-800 hover:border-slate-700 bg-slate-950/60' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="inline-block px-2.5 py-1 bg-blue-600 text-white text-[10px] font-black rounded-lg uppercase tracking-wider mb-2">Layanan Utama</span>
                                <h3 class="font-extrabold text-white text-lg">Laundry Kiloan (Per kg)</h3>
                                <p class="text-xs text-slate-400 mt-1">Hanya untuk pakaian sehari-hari (Kaos, Kemeja, Celana, Rok, Jeans, Jaket). Harga berdasar berat akhir.</p>
                            </div>
                        </div>
                    </button>

                    <button type="button" wire:click="selectServiceType('per_item')" class="p-5 rounded-2xl border-2 text-left transition-all relative overflow-hidden cursor-pointer {{ $service_type === 'per_item' ? 'border-cyan-500 bg-cyan-500/10 shadow-lg ring-1 ring-cyan-500/30' : 'border-slate-800 hover:border-slate-700 bg-slate-950/60' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="inline-block px-2.5 py-1 bg-sky-600 text-white text-[10px] font-black rounded-lg uppercase tracking-wider mb-2">Khusus Item</span>
                                <h3 class="font-extrabold text-white text-lg">Laundry Per Item / Satuan</h3>
                                <p class="text-xs text-slate-400 mt-1">Khusus Jas, Sepatu, Selimut, Bed Cover, Karpet & item khusus per-pcs.</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- 2. Package Option (Ekonomis vs Premium) -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">2. Pilih Paket Laundry</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button type="button" wire:click="selectPackageType('ekonomis')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $package_type === 'ekonomis' ? 'border-emerald-500 bg-emerald-500/10 shadow-md ring-1 ring-emerald-500/30' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-extrabold text-white text-base">Paket Ekonomis / Hemat</h4>
                                <p class="text-xs text-slate-400">
                                    @if($service_type === 'kiloan')
                                        Cuci higienis + detergent standar + lipat rapi
                                    @else
                                        Perawatan & pencucian standar higienis per item
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-black text-emerald-400 bg-emerald-500/20 border border-emerald-500/30 px-3 py-1 rounded-xl">
                                @if($service_type === 'kiloan')
                                    Rp8.000 / kg
                                @else
                                    Tarif Normal
                                @endif
                            </span>
                        </div>
                    </button>

                    <button type="button" wire:click="selectPackageType('premium')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $package_type === 'premium' ? 'border-amber-500 bg-amber-500/10 shadow-md ring-1 ring-amber-500/30' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-extrabold text-white text-base">Paket Premium</h4>
                                <p class="text-xs text-slate-400">
                                    @if($service_type === 'kiloan')
                                        Softener harum + setrika halus + disinfektan
                                    @else
                                        Softener harum tahan lama + disinfektan & anti-bakteri
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-black text-amber-400 bg-amber-500/20 border border-amber-500/30 px-3 py-1 rounded-xl">
                                @if($service_type === 'kiloan')
                                    Rp12.000 / kg
                                @else
                                    Premium (+25%)
                                @endif
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- 3. Wash Option Selector -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">3. Pilih Metode Pengerjaan Laundry</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button type="button" wire:click="selectWashOption('cuci_setrika')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $wash_option === 'cuci_setrika' ? 'border-cyan-500 bg-cyan-500/10 shadow-md ring-1 ring-cyan-500/30' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-white text-sm">Cuci & Setrika</h4>
                            <p class="text-[11px] text-slate-400">Cuci bersih + parfum + setrika rapi (Lengkap).</p>
                        </div>
                    </button>

                    <button type="button" wire:click="selectWashOption('cuci_lipat')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $wash_option === 'cuci_lipat' ? 'border-emerald-500 bg-emerald-500/10 shadow-md ring-1 ring-emerald-500/30' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-white text-sm">Cuci & Lipat</h4>
                            <p class="text-[11px] text-slate-400">Cuci bersih + lipat rapi tanpa setrika (Hemat).</p>
                        </div>
                    </button>

                    <button type="button" wire:click="selectWashOption('cuci_saja')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $wash_option === 'cuci_saja' ? 'border-sky-500 bg-sky-500/10 shadow-md ring-1 ring-sky-500/30' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-white text-sm">Cuci Saja</h4>
                            <p class="text-[11px] text-slate-400">Cuci bersih & kering saja tanpa lipat/setrika.</p>
                        </div>
                    </button>

                    <button type="button" wire:click="selectWashOption('setrika_saja')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $wash_option === 'setrika_saja' ? 'border-amber-500 bg-amber-500/10 shadow-md ring-1 ring-amber-500/30' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-white text-sm">Setrika Saja</h4>
                            <p class="text-[11px] text-slate-400">Pakaian bersih, disetrika & disemprot harum.</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- 4. Speed Option -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">4. Pilih Kecepatan Pengerjaan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button type="button" wire:click="selectSpeedType('reguler')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $speed_type === 'reguler' ? 'border-cyan-500 bg-cyan-500/10 shadow-md' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-base font-extrabold text-white">Reguler</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-500/20 text-cyan-300 border border-blue-500/30">Estimasi 2 Hari</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Harga normal standar layanan.</p>
                            </div>
                            <span class="text-xs font-bold text-slate-400">Standard</span>
                        </div>
                    </button>

                    <button type="button" wire:click="selectSpeedType('ekspres')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $speed_type === 'ekspres' ? 'border-purple-500 bg-purple-500/10 shadow-md' : 'border-slate-800 bg-slate-950/60' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-base font-extrabold text-white">Ekspres</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">Estimasi 24 Jam</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Selesai dalam 24 jam (+30-50% biaya ekspres).</p>
                            </div>
                            <span class="text-xs font-bold text-purple-300">+30–50%</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- 5. Item List Selection -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">5. Pilih Pakaian / Item Laundry</label>
                    <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $service_type === 'kiloan' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-sky-500/10 text-sky-400 border-sky-500/30' }}">
                        Mode: {{ $service_type === 'kiloan' ? 'Pakaian Sehari-hari (Kiloan)' : 'Layanan Satuan / Per Item' }}
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($services as $service)
                        @php
                            $badge = $service->type_badge;
                        @endphp
                        <div wire:key="service-item-{{ $service->id }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl border transition-all gap-3 bg-slate-950/80 border-slate-800 hover:border-slate-700">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-extrabold text-white text-base">{{ $service->name }}</h4>
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full border bg-blue-500/10 text-cyan-400 border-blue-500/30">
                                        {{ $badge['label'] }}
                                    </span>
                                    @if($service_type === 'kiloan')
                                        <span class="text-[11px] text-slate-400 bg-slate-900 border border-slate-800 px-2 py-0.5 rounded font-mono">
                                            Ref berat: ±{{ \App\Services\PricingService::ITEM_WEIGHTS[strtolower($service->name)] ?? 0.3 }} kg
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 max-w-md">{{ $service->description }}</p>

                                @if($service_type === 'per_item')
                                    @php
                                        $baseP = $speed_type === 'ekspres' && $service->express_price > 0 ? $service->express_price : $service->price;
                                        $displayPrice = app(\App\Services\PricingService::class)->getPerItemUnitPrice($baseP, $package_type, $wash_option);
                                    @endphp
                                    <span class="inline-block text-sm font-black text-cyan-400">
                                        @if($displayPrice > 0)
                                            Rp{{ number_format($displayPrice, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">/ item</span>
                                        @else
                                            <span class="text-amber-400 font-bold">Harga Admin</span>
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <!-- Interactive Quantity Controls -->
                            <div class="flex items-center space-x-2 bg-slate-900 p-1.5 rounded-xl border border-slate-800 self-start sm:self-center">
                                <button type="button" wire:key="btn-dec-{{ $service->id }}" wire:click="decrementQuantity({{ $service->id }})" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-slate-700 active:scale-95 font-black text-white text-xl flex items-center justify-center transition-all cursor-pointer">
                                    -
                                </button>

                                <input type="number" min="0" wire:key="qty-input-{{ $service->id }}" value="{{ $quantities[$service->id] ?? 0 }}" wire:change="updateQuantity({{ $service->id }}, $event.target.value)" class="w-14 text-center font-extrabold text-white text-base bg-slate-950 border border-slate-800 rounded-lg py-1 focus:ring-1 focus:ring-cyan-500 focus:outline-none">

                                <button type="button" wire:key="btn-inc-{{ $service->id }}" wire:click="incrementQuantity({{ $service->id }})" class="w-9 h-9 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white active:scale-95 font-black text-xl flex items-center justify-center shadow-md transition-all cursor-pointer">
                                    +
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Custom Item Text Inputs (if Item Lainnya is selected) -->
            @if($has_custom_item)
                <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-4 text-amber-300">
                    <div class="flex items-center space-x-2 font-bold text-sm">
                        <span>Details Item Lainnya:</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Barang</label>
                            <input type="text" wire:model.blur="custom_item_name" placeholder="Contoh: Boneka Besar / Gorden..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                            @error('custom_item_name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Jumlah Barang</label>
                            <input type="number" min="1" wire:model.blur="custom_item_qty" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-bold text-white focus:outline-none focus:border-cyan-500">
                            @error('custom_item_qty') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model.blur="custom_item_notes" placeholder="Deskripsi kondisi, bahan, atau permintaan khusus..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
            @endif

            <!-- 5. Automated Price Estimation Box -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-blue-950 border border-slate-800 text-white shadow-2xl space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-cyan-400">ESTIMASI BIAYA ORDER</span>
                        <h3 class="text-xl font-black text-white">Ringkasan Estimasi Otomatis</h3>
                    </div>
                    <span class="px-3 py-1 bg-blue-500/20 border border-blue-500/30 text-cyan-300 rounded-full text-xs font-bold uppercase">
                        {{ strtoupper($service_type) }} &bull; {{ strtoupper($speed_type) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                    @if($service_type === 'kiloan')
                        <div>
                            <span class="text-xs text-slate-400 block">Estimasi Berat Laundry:</span>
                            <span class="text-3xl font-black text-white">±{{ number_format($estimatedWeight, 1, ',', '.') }} kg</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Rate: Rp{{ number_format($pricePerKg, 0, ',', '.') }} / kg</span>
                        </div>
                    @endif

                    <div>
                        <span class="text-xs text-slate-400 block">Estimasi Biaya Total:</span>
                        <span class="text-3xl font-black text-cyan-400">Rp{{ number_format($estimatedPrice, 0, ',', '.') }}</span>
                        <span class="text-[11px] text-emerald-400 block mt-0.5">Ongkir Antar-Jemput: GRATIS</span>
                    </div>
                </div>

                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 text-xs text-slate-400 font-medium">
                    *Harga final mengikuti berat aktual / penimbangan akhir setelah proses laundry selesai oleh admin.
                </div>
            </div>

            <!-- Footer Navigation -->
            <div class="pt-6 border-t border-slate-800 flex justify-end">
                <button type="button" wire:click="goToStep2" class="px-8 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-black text-base rounded-2xl shadow-xl transition-all hover:scale-105 cursor-pointer">
                    Lanjut ke Lokasi Pickup &rarr;
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 2: Pickup Details -->
    @if($step == 2)
        <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl backdrop-blur-xl space-y-6 text-slate-100">
            <div>
                <h2 class="text-2xl font-black text-white">Step 2: Alamat & Jadwal Penjemputan</h2>
                <p class="text-xs text-slate-400 mt-1">Masukkan nomor telepon aktif dan alamat penjemputan laundry Anda.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nama Pemesan</label>
                    <input type="text" wire:model="pickup_name" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                    @error('pickup_name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nomor Telepon Aktif / WhatsApp</label>
                    <input type="text" wire:model="pickup_phone" placeholder="081234567890" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                    @error('pickup_phone') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div x-data="{
                    locating: false,
                    showModal: false,
                    searchQuery: '',
                    lat: -6.2000,
                    lng: 106.8166,
                    addressResult: '',
                    map: null,
                    marker: null,

                    initMap() {
                        this.$nextTick(() => {
                            if (!this.map) {
                                this.map = L.map(this.$refs.mapContainer).setView([this.lat, this.lng], 14);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 19,
                                    attribution: '© OpenStreetMap'
                                }).addTo(this.map);

                                this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);

                                this.marker.on('dragend', () => {
                                    const pos = this.marker.getLatLng();
                                    this.lat = pos.lat;
                                    this.lng = pos.lng;
                                    this.reverseGeocode();
                                });

                                this.map.on('click', (e) => {
                                    this.lat = e.latlng.lat;
                                    this.lng = e.latlng.lng;
                                    this.marker.setLatLng(e.latlng);
                                    this.reverseGeocode();
                                });
                            } else {
                                setTimeout(() => { this.map.invalidateSize(); }, 200);
                            }
                        });
                    },

                    openMap() {
                        this.showModal = true;
                        this.initMap();
                    },

                    searchLocation() {
                        if (!this.searchQuery) return;
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    this.lat = parseFloat(data[0].lat);
                                    this.lng = parseFloat(data[0].lon);
                                    this.addressResult = data[0].display_name;
                                    if (this.map && this.marker) {
                                        this.map.setView([this.lat, this.lng], 15);
                                        this.marker.setLatLng([this.lat, this.lng]);
                                    }
                                } else {
                                    alert('Lokasi tidak ditemukan. Coba ketik nama jalan / area lain.');
                                }
                            });
                    },

                    reverseGeocode(wireField = null) {
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.lat}&lon=${this.lng}`)
                            .then(res => res.json())
                            .then(data => {
                                this.addressResult = data.display_name || `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                                if (wireField) {
                                    let full = `${this.addressResult}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                                    $wire.set(wireField, full);
                                }
                            }).catch(() => {
                                this.addressResult = `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                                if (wireField) {
                                    let full = `${this.addressResult}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                                    $wire.set(wireField, full);
                                }
                            });
                    },

                    detectLocation(wireField = null) {
                        if (!navigator.geolocation) {
                            alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
                            return;
                        }
                        this.locating = true;
                        navigator.geolocation.getCurrentPosition((position) => {
                            this.lat = position.coords.latitude;
                            this.lng = position.coords.longitude;
                            this.locating = false;
                            this.reverseGeocode(wireField);
                            if (this.map && this.marker) {
                                this.map.setView([this.lat, this.lng], 16);
                                this.marker.setLatLng([this.lat, this.lng]);
                            }
                        }, (error) => {
                            this.locating = false;
                            alert('Gagal mendeteksi lokasi. Pastikan GPS/izin lokasi diaktifkan di browser Anda.');
                        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                    },

                    confirmLocation(wireField) {
                        let addr = this.addressResult || `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                        let full = `${addr}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                        $wire.set(wireField, full);
                        this.showModal = false;
                    }
                }" class="space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase">Alamat Penjemputan Lengkap</label>
                        
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="openMap()" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-md cursor-pointer">
                                <span>Pilih / Geser Pin di Peta</span>
                            </button>

                            <button type="button" @click="detectLocation('pickup_address')" :disabled="locating" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs font-bold transition-all border border-emerald-500/30 cursor-pointer">
                                <span x-show="!locating">Lokasi Saat Ini</span>
                                <span x-show="locating" style="display:none;">Mendeteksi...</span>
                            </button>
                        </div>
                    </div>

                    <textarea wire:model.live.debounce.500ms="pickup_address" rows="3" placeholder="Contoh: Jl. Raya Utama No. 123, RT 02/RW 05, Patokan dekat Masjid Al-Ikhlas..." class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500"></textarea>
                    @error('pickup_address') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror

                    <!-- Interactive Map Modal -->
                    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                        <div @click.away="showModal = false" class="bg-slate-900 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-800 relative text-slate-100">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                <h3 class="text-base font-black text-white flex items-center gap-2">
                                    <span>Pilih Titik Lokasi Tepat di Peta</span>
                                    <span class="text-xs font-bold text-cyan-400 bg-blue-500/20 px-2 py-0.5 rounded-full border border-blue-500/30">Pilih Lokasi</span>
                                </h3>
                                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                            </div>

                            <!-- Search Bar in Modal -->
                            <div class="flex gap-2">
                                <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchLocation()" placeholder="Ketik nama jalan / area (contoh: Pajajaran, Margonda, Sudirman)..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-white focus:outline-none focus:border-cyan-500">
                                <button type="button" @click="searchLocation()" class="px-4 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-500 transition-all shrink-0">
                                    Cari
                                </button>
                            </div>

                            <!-- Map Container -->
                            <div x-ref="mapContainer" class="w-full h-72 rounded-2xl border border-slate-800 shadow-inner"></div>

                            <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-xs">
                                <span class="font-bold text-slate-400 block mb-1">Alamat Terpilih:</span>
                                <p x-text="addressResult || 'Geser atau klik pin pada peta untuk memilih alamat tepat.'" class="text-slate-300 font-medium"></p>
                            </div>

                            <div class="flex justify-between items-center pt-2">
                                <button type="button" @click="detectLocation()" class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-3 py-2 rounded-xl border border-emerald-500/30 hover:bg-emerald-500/20 transition-all">
                                    Lokasi Saat Ini
                                </button>
                                <div class="flex space-x-2">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                                        Batal
                                    </button>
                                    <button type="button" @click="confirmLocation('pickup_address')" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md">
                                        Gunakan Lokasi Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tanggal Pickup</label>
                        <input type="date" wire:model="pickup_date" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                        @error('pickup_date') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Jam Pickup</label>
                        <select wire:model="pickup_time" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                            <option value="09:00">09:00 WIB</option>
                            <option value="11:00">11:00 WIB</option>
                            <option value="14:00">14:00 WIB</option>
                            <option value="16:00">16:00 WIB</option>
                            <option value="19:00">19:00 WIB</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea wire:model="pickup_notes" rows="2" placeholder="Petunjuk lokasi, lantai, pagar warna hitam..." class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500"></textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 flex justify-between">
                <button type="button" wire:click="$set('step', 1)" class="px-5 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-sm rounded-2xl border border-slate-700 cursor-pointer">
                    &larr; Kembali
                </button>
                <button type="button" wire:click="goToStep3" class="px-7 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-sm rounded-2xl shadow-lg transition-all cursor-pointer">
                    Lanjut ke Delivery &rarr;
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 3: Delivery Details -->
    @if($step == 3)
        <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl backdrop-blur-xl space-y-6 text-slate-100">
            <div>
                <h2 class="text-2xl font-black text-white">Step 3: Alamat Pengantaran (Delivery)</h2>
                <p class="text-xs text-slate-400 mt-1">Konfirmasi lokasi pengembalian laundry setelah selesai.</p>
            </div>

            <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/30 flex items-center space-x-3">
                <input type="checkbox" id="sameAsPickup" wire:model.live="sameAsPickup" class="w-5 h-5 rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500">
                <label for="sameAsPickup" class="text-sm font-extrabold text-white cursor-pointer">
                    Sama dengan Alamat Penjemputan (Pickup)
                </label>
            </div>

            @if($sameAsPickup)
                <div class="p-4.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 space-y-2 text-xs">
                    <div class="flex items-center space-x-2 font-bold text-emerald-300 text-sm">
                        <span>Alamat & Kontak Pengantaran Disamakan dengan Pickup</span>
                    </div>
                    <div class="text-emerald-200 space-y-1 font-medium">
                        <p><strong>Penerima:</strong> {{ $pickup_name }} ({{ $pickup_phone }})</p>
                        <p><strong>Alamat Pengantaran:</strong> {{ $pickup_address }}</p>
                        <p><strong>Estimasi Tanggal Pengantaran:</strong> {{ \Carbon\Carbon::parse($delivery_date)->format('d M Y') }} (Jam {{ $delivery_time }})</p>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nama Penerima Delivery</label>
                        <input type="text" wire:model="delivery_name" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                        @error('delivery_name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nomor Telepon Penerima</label>
                        <input type="text" wire:model="delivery_phone" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                        @error('delivery_phone') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{
                        locating: false,
                        showModal: false,
                        searchQuery: '',
                        lat: -6.2000,
                        lng: 106.8166,
                        addressResult: '',
                        map: null,
                        marker: null,

                        initMap() {
                            this.$nextTick(() => {
                                if (!this.map) {
                                    this.map = L.map(this.$refs.mapContainer).setView([this.lat, this.lng], 14);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 19,
                                        attribution: '© OpenStreetMap'
                                    }).addTo(this.map);

                                    this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);

                                    this.marker.on('dragend', () => {
                                        const pos = this.marker.getLatLng();
                                        this.lat = pos.lat;
                                        this.lng = pos.lng;
                                        this.reverseGeocode();
                                    });

                                    this.map.on('click', (e) => {
                                        this.lat = e.latlng.lat;
                                        this.lng = e.latlng.lng;
                                        this.marker.setLatLng(e.latlng);
                                        this.reverseGeocode();
                                    });
                                } else {
                                    setTimeout(() => { this.map.invalidateSize(); }, 200);
                                }
                            });
                        },

                        openMap() {
                            this.showModal = true;
                            this.initMap();
                        },

                        searchLocation() {
                            if (!this.searchQuery) return;
                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data && data.length > 0) {
                                        this.lat = parseFloat(data[0].lat);
                                        this.lng = parseFloat(data[0].lon);
                                        this.addressResult = data[0].display_name;
                                        if (this.map && this.marker) {
                                            this.map.setView([this.lat, this.lng], 15);
                                            this.marker.setLatLng([this.lat, this.lng]);
                                        }
                                    } else {
                                        alert('Lokasi tidak ditemukan. Coba ketik nama jalan / area lain.');
                                    }
                                });
                        },

                        reverseGeocode(wireField = null) {
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.lat}&lon=${this.lng}`)
                                .then(res => res.json())
                                .then(data => {
                                    this.addressResult = data.display_name || `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                                    if (wireField) {
                                        let full = `${this.addressResult}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                                        $wire.set(wireField, full);
                                    }
                                }).catch(() => {
                                    this.addressResult = `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                                    if (wireField) {
                                        let full = `${this.addressResult}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                                        $wire.set(wireField, full);
                                    }
                                });
                        },

                        detectLocation(wireField = null) {
                            if (!navigator.geolocation) {
                                alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
                                return;
                            }
                            this.locating = true;
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.lat = position.coords.latitude;
                                this.lng = position.coords.longitude;
                                this.locating = false;
                                this.reverseGeocode(wireField);
                                if (this.map && this.marker) {
                                    this.map.setView([this.lat, this.lng], 16);
                                    this.marker.setLatLng([this.lat, this.lng]);
                                }
                            }, (error) => {
                                this.locating = false;
                                alert('Gagal mendeteksi lokasi. Pastikan GPS/izin lokasi diaktifkan di browser Anda.');
                            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                        },

                        confirmLocation(wireField) {
                            let addr = this.addressResult || `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                            let full = `${addr}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                            $wire.set(wireField, full);
                            this.showModal = false;
                        }
                    }" class="space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase">Alamat Pengantaran Lengkap</label>
                            
                            <div class="flex items-center space-x-2">
                                <button type="button" @click="openMap()" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-md cursor-pointer">
                                    <span>Pilih / Geser Pin di Peta</span>
                                </button>

                                <button type="button" @click="detectLocation('delivery_address')" :disabled="locating" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs font-bold transition-all border border-emerald-500/30 cursor-pointer">
                                    <span x-show="!locating">Lokasi Saat Ini</span>
                                    <span x-show="locating" style="display:none;">Mendeteksi...</span>
                                </button>
                            </div>
                        </div>

                        <textarea wire:model.live.debounce.500ms="delivery_address" rows="3" placeholder="Contoh: Jl. Raya Utama No. 123, RT 02/RW 05, Patokan dekat Masjid Al-Ikhlas..." class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500"></textarea>
                        @error('delivery_address') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror

                        <!-- Interactive Map Modal -->
                        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                            <div @click.away="showModal = false" class="bg-slate-900 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-800 relative text-slate-100">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                    <h3 class="text-base font-black text-white flex items-center gap-2">
                                        <span>Pilih Titik Lokasi Tepat di Peta</span>
                                        <span class="text-xs font-bold text-cyan-400 bg-blue-500/20 px-2 py-0.5 rounded-full border border-blue-500/30">Pilih Lokasi</span>
                                    </h3>
                                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                                </div>

                                <!-- Search Bar in Modal -->
                                <div class="flex gap-2">
                                    <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchLocation()" placeholder="Ketik nama jalan / area (contoh: Pajajaran, Margonda, Sudirman)..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-white focus:outline-none focus:border-cyan-500">
                                    <button type="button" @click="searchLocation()" class="px-4 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-500 transition-all shrink-0">
                                        Cari
                                    </button>
                                </div>

                                <!-- Map Container -->
                                <div x-ref="mapContainer" class="w-full h-72 rounded-2xl border border-slate-800 shadow-inner"></div>

                                <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-xs">
                                    <span class="font-bold text-slate-400 block mb-1">Alamat Terpilih:</span>
                                    <p x-text="addressResult || 'Geser atau klik pin pada peta untuk memilih alamat tepat.'" class="text-slate-300 font-medium"></p>
                                </div>

                                <div class="flex justify-between items-center pt-2">
                                    <button type="button" @click="detectLocation()" class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-3 py-2 rounded-xl border border-emerald-500/30 hover:bg-emerald-500/20 transition-all">
                                        Lokasi Saat Ini
                                    </button>
                                    <div class="flex space-x-2">
                                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                                            Batal
                                        </button>
                                        <button type="button" @click="confirmLocation('delivery_address')" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md">
                                            Gunakan Lokasi Ini
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tanggal Delivery (Pengantaran)</label>
                            <input type="date" wire:model="delivery_date" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                            @error('delivery_date') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Jam Delivery</label>
                            <select wire:model="delivery_time" class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500">
                                <option value="09:00">09:00 WIB</option>
                                <option value="11:00">11:00 WIB</option>
                                <option value="14:00">14:00 WIB (Default)</option>
                                <option value="16:00">16:00 WIB</option>
                                <option value="19:00">19:00 WIB</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Catatan Delivery (Opsional)</label>
                <textarea wire:model="delivery_notes" rows="2" placeholder="Catatan khusus untuk driver pengiriman..." class="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-sm font-semibold text-white focus:outline-none focus:border-cyan-500"></textarea>
            </div>

            <div class="pt-6 border-t border-slate-800 flex justify-between">
                <button type="button" wire:click="$set('step', 2)" class="px-5 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-sm rounded-2xl border border-slate-700 cursor-pointer">
                    &larr; Kembali
                </button>
                <button type="button" wire:click="submitOrder" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-base rounded-2xl shadow-xl transition-all cursor-pointer">
                    Submit Order &rarr;
                </button>
            </div>
        </div>
    @endif
</div>
