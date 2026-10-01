<div class="max-w-3xl mx-auto space-y-8">
    <!-- Wizard Progress Stepper -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between">
            <button type="button" @if($step > 1) wire:click="$set('step', 1)" @endif class="flex items-center space-x-2 text-left focus:outline-none">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }}">1</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 1 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">Pilih Item</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-200 mx-2"></div>

            <button type="button" @if($step > 2) wire:click="$set('step', 2)" @endif class="flex items-center space-x-2 text-left focus:outline-none">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }}">2</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 2 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">Pickup</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-200 mx-2"></div>

            <button type="button" @if($step > 3) wire:click="$set('step', 3)" @endif class="flex items-center space-x-2 text-left focus:outline-none">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }}">3</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 3 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">Delivery</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-200 mx-2"></div>

            <button type="button" @if($step > 4) wire:click="$set('step', 4)" @endif class="flex items-center space-x-2 text-left focus:outline-none">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all {{ $step >= 4 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }}">4</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step >= 4 ? 'text-slate-900 font-bold' : 'text-slate-400' }}">Review</span>
            </button>
            <div class="h-0.5 flex-1 bg-slate-200 mx-2"></div>

            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center {{ $step == 5 ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' }}">5</div>
                <span class="text-xs font-semibold hidden sm:inline {{ $step == 5 ? 'text-emerald-700 font-bold' : 'text-slate-400' }}">QRIS Demo</span>
            </div>
        </div>
    </div>

    <!-- STEP 1: Select Items -->
    @if($step == 1)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h2 class="text-xl font-black text-slate-900">Step 1: Pilih Item Laundry</h2>
                <p class="text-xs text-slate-500 mt-1">Tentukan jumlah item laundry (Harga per buah/piece, bukan kiloan).</p>
            </div>

            @if($step1_error || session()->has('step1_error'))
                <div class="p-4 bg-rose-50 border-2 border-rose-300 text-rose-800 rounded-2xl text-xs sm:text-sm font-extrabold flex items-center space-x-3 shadow-xs animate-bounce">
                    <span class="text-xl">⚠️</span>
                    <span>{{ $step1_error ?: session('step1_error') }}</span>
                </div>
            @endif

            <div class="space-y-4">
                @foreach($services as $service)
                    <div wire:key="service-item-{{ $service->id }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-300 transition-colors">
                        <div class="space-y-1">
                            <h4 class="font-extrabold text-slate-900 text-base">{{ $service->name }}</h4>
                            <p class="text-xs text-slate-500 max-w-md">{{ $service->description }}</p>
                            <span class="inline-block text-sm font-black text-blue-600">Rp{{ number_format($service->price, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">/ pcs</span></span>
                        </div>

                        <!-- Interactive Active Quantity Control (Direct Input + Touch Buttons) -->
                        <div class="flex items-center space-x-2 bg-white p-1.5 rounded-xl border border-slate-200 shadow-xs">
                            <button type="button" wire:key="btn-dec-{{ $service->id }}" wire:click="decrementQuantity({{ $service->id }})" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 active:scale-95 font-black text-slate-700 text-xl flex items-center justify-center transition-all select-none cursor-pointer">
                                -
                            </button>
                            
                            <input type="number" min="0" wire:key="qty-input-{{ $service->id }}" value="{{ $quantities[$service->id] ?? 0 }}" wire:change="updateQuantity({{ $service->id }}, $event.target.value)" class="w-14 text-center font-extrabold text-slate-900 text-base bg-slate-50 border border-slate-200 rounded-lg py-1 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                            <button type="button" wire:key="btn-inc-{{ $service->id }}" wire:click="incrementQuantity({{ $service->id }})" class="w-9 h-9 rounded-lg bg-blue-600 hover:bg-blue-700 active:scale-95 font-black text-white text-xl flex items-center justify-center shadow-xs transition-all select-none cursor-pointer">
                                +
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Cart Summary Footer -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase block">Estimasi Subtotal</span>
                    <span class="text-2xl font-black text-blue-600">Rp{{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                </div>
                <button type="button" wire:click="goToStep2" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-2xl shadow-lg transition-all hover:scale-105">
                    Lanjut ke Lokasi Pickup &rarr;
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 2: Pickup Details -->
    @if($step == 2)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h2 class="text-xl font-black text-slate-900">Step 2: Alamat & Jadwal Penjemputan</h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan informasi kontak dan jadwal jemput laundry Anda.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Pemesan</label>
                    <input type="text" wire:model="pickup_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                    @error('pickup_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" wire:model="pickup_phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                    @error('pickup_phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Penjemputan Lengkap</label>
                    <textarea wire:model="pickup_address" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium"></textarea>
                    @error('pickup_address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Pickup</label>
                        <input type="date" wire:model="pickup_date" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                        @error('pickup_date') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Pickup</label>
                        <select wire:model="pickup_time" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                            <option value="09:00">09:00 WIB</option>
                            <option value="11:00">11:00 WIB</option>
                            <option value="14:00">14:00 WIB</option>
                            <option value="16:00">16:00 WIB</option>
                            <option value="19:00">19:00 WIB</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea wire:model="pickup_notes" rows="2" placeholder="Petunjuk lokasi, lantai, patokan rumah..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium"></textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-between">
                <button type="button" wire:click="$set('step', 1)" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl">
                    &larr; Kembali
                </button>
                <button type="button" wire:click="goToStep3" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-2xl shadow-lg transition-all">
                    Lanjut ke Lokasi Delivery &rarr;
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 3: Delivery Details -->
    @if($step == 3)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h2 class="text-xl font-black text-slate-900">Step 3: Alamat Pengantaran (Delivery)</h2>
                <p class="text-xs text-slate-500 mt-1">Tentukan lokasi di mana laundry bersih akan diantarkan.</p>
            </div>

            <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-center space-x-3">
                <input type="checkbox" id="sameAsPickup" wire:model.live="sameAsPickup" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                <label for="sameAsPickup" class="text-sm font-bold text-slate-800 cursor-pointer">
                    Sama dengan Alamat Penjemputan (Pickup)
                </label>
            </div>

            @if(!$sameAsPickup)
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Penerima Delivery</label>
                        <input type="text" wire:model="delivery_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                        @error('delivery_name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon Penerima</label>
                        <input type="text" wire:model="delivery_phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium">
                        @error('delivery_phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Pengantaran Lengkap</label>
                        <textarea wire:model="delivery_address" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium"></textarea>
                        @error('delivery_address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Delivery (Opsional)</label>
                <textarea wire:model="delivery_notes" rows="2" placeholder="Titip di pos sekuriti / tetangga bila tidak ada..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium"></textarea>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-between">
                <button type="button" wire:click="$set('step', 2)" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl">
                    &larr; Kembali
                </button>
                <button type="button" wire:click="goToStep4" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-2xl shadow-lg transition-all">
                    Review Order &rarr;
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 4: Review Order -->
    @if($step == 4)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div>
                <h2 class="text-xl font-black text-slate-900">Step 4: Review Pesanan Laundry</h2>
                <p class="text-xs text-slate-500 mt-1">Periksa kembali item, alamat, dan total biaya sebelum ke pembayaran.</p>
            </div>

            <!-- Items Breakdown Table -->
            <div class="space-y-3">
                <h4 class="font-extrabold text-slate-900 text-sm">Item Laundry</h4>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 divide-y divide-slate-200/60">
                    @foreach($selectedItems as $item)
                        <div wire:key="review-item-{{ $item['service']->id }}" class="py-2.5 flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-slate-800 text-sm">{{ $item['service']->name }}</span>
                                <span class="text-slate-400 block font-mono">Rp{{ number_format($item['service']->price, 0, ',', '.') }} x {{ $item['quantity'] }} pcs</span>
                            </div>
                            <span class="font-black text-slate-900 text-sm">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Address Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-blue-600 font-extrabold block uppercase mb-1">🚚 Penjemputan (Pickup)</span>
                    <p class="font-bold text-slate-800 text-sm">{{ $pickup_name }} ({{ $pickup_phone }})</p>
                    <p class="text-slate-600 mt-1">{{ $pickup_address }}</p>
                    <p class="text-slate-500 font-semibold mt-2">Jadwal: {{ date('d M Y', strtotime($pickup_date)) }} - {{ $pickup_time }} WIB</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-sky-600 font-extrabold block uppercase mb-1">📦 Pengantaran (Delivery)</span>
                    <p class="font-bold text-slate-800 text-sm">{{ $sameAsPickup ? $pickup_name : $delivery_name }} ({{ $sameAsPickup ? $pickup_phone : $delivery_phone }})</p>
                    <p class="text-slate-600 mt-1">{{ $sameAsPickup ? $pickup_address : $delivery_address }}</p>
                </div>
            </div>

            <!-- Pricing Summary Card -->
            <div class="p-5 rounded-2xl bg-blue-50/70 border border-blue-100 space-y-2 text-sm">
                <div class="flex justify-between text-slate-700">
                    <span>Subtotal Items</span>
                    <span class="font-bold">Rp{{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-emerald-600 font-extrabold">
                    <span>Ongkos Kirim Pickup & Delivery</span>
                    <span>GRATIS (Rp 0)</span>
                </div>
                <div class="pt-3 border-t border-blue-200 flex justify-between items-center text-lg font-black text-slate-900">
                    <span>TOTAL BAYAR</span>
                    <span class="text-blue-700 text-2xl">Rp{{ number_format($totals['total'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-between">
                <button type="button" wire:click="$set('step', 3)" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl">
                    &larr; Kembali
                </button>
                <button type="button" wire:click="submitOrder" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm rounded-2xl shadow-xl transition-all">
                    Lanjut ke QRIS Demo &rarr;
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 5: DEMO QRIS PAYMENT VIEW (PRD Section 19 & 20) -->
    @if($step == 5 && $createdOrder)
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-xl space-y-6 text-center">
            <div class="inline-flex items-center space-x-2 px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-black tracking-wider uppercase">
                <span>DEMO PAYMENT — QRIS SIMULATION</span>
            </div>

            <h2 class="text-2xl font-black text-slate-900">PEMBAYARAN QRIS</h2>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Scan QR Code di bawah ini untuk simulasi pembayaran QRIS instan.</p>

            <div class="p-4 bg-slate-50 rounded-3xl border border-slate-200 max-w-sm mx-auto space-y-3">
                <span class="text-xs text-slate-400 font-bold block">TOTAL PEMBAYARAN</span>
                <p class="text-3xl font-black text-blue-600">Rp{{ number_format($createdOrder->total, 0, ',', '.') }}</p>

                <!-- Dummy QRIS Image -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200 inline-block shadow-inner">
                    <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                        <rect x="5" y="5" width="90" height="90" rx="6" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="2"/>
                        <!-- QR Corner Squares -->
                        <rect x="15" y="15" width="22" height="22" fill="#1E293B"/>
                        <rect x="19" y="19" width="14" height="14" fill="#FFFFFF"/>
                        <rect x="22" y="22" width="8" height="8" fill="#1E293B"/>

                        <rect x="63" y="15" width="22" height="22" fill="#1E293B"/>
                        <rect x="67" y="19" width="14" height="14" fill="#FFFFFF"/>
                        <rect x="70" y="22" width="8" height="8" fill="#1E293B"/>

                        <rect x="15" y="63" width="22" height="22" fill="#1E293B"/>
                        <rect x="19" y="67" width="14" height="14" fill="#FFFFFF"/>
                        <rect x="22" y="70" width="8" height="8" fill="#1E293B"/>

                        <!-- QR Pattern Modules -->
                        <rect x="45" y="15" width="10" height="10" fill="#4F46E5"/>
                        <rect x="45" y="30" width="10" height="10" fill="#1E293B"/>
                        <rect x="15" y="45" width="10" height="10" fill="#1E293B"/>
                        <rect x="30" y="45" width="10" height="10" fill="#4F46E5"/>
                        <rect x="45" y="45" width="10" height="10" fill="#1E293B"/>
                        <rect x="60" y="45" width="10" height="10" fill="#4F46E5"/>
                        <rect x="75" y="45" width="10" height="10" fill="#1E293B"/>
                        <rect x="45" y="60" width="10" height="10" fill="#1E293B"/>
                        <rect x="60" y="60" width="10" height="10" fill="#4F46E5"/>
                        <rect x="45" y="75" width="10" height="10" fill="#1E293B"/>
                        <rect x="60" y="75" width="10" height="10" fill="#1E293B"/>
                        <rect x="75" y="60" width="10" height="10" fill="#4F46E5"/>
                    </svg>
                </div>

                <div class="text-[11px] font-mono text-slate-500">
                    Ref: {{ $createdOrder->payment->payment_reference ?? 'DEMO-QRIS' }}
                </div>
            </div>

            <!-- "Saya Sudah Membayar" Action Button -->
            <div class="pt-4 max-w-sm mx-auto">
                <button type="button" wire:click="processDemoPayment" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-base rounded-2xl shadow-xl shadow-emerald-500/25 transition-all hover:scale-105">
                    [ Saya Sudah Membayar ]
                </button>
            </div>
        </div>
    @endif
</div>
