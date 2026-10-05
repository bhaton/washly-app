<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.index') }}" class="p-2 bg-slate-900 rounded-xl border border-slate-800 text-slate-400 hover:text-white transition-colors">
                    &larr;
                </a>
                <h1 class="text-2xl font-black text-white tracking-tight">Detail Order {{ $order->order_number }}</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Dibuat pada {{ $order->created_at?->format('d F Y, H:i') }} WIB</p>
        </div>

        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold shadow-lg
                @if(in_array($order->status, ['ORDER_SELESAI', 'COMPLETED'])) bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                @elseif(in_array($order->status, ['MENUNGGU_PICKUP', 'DRIVER_DITUGASKAN'])) bg-amber-500/10 text-amber-400 border border-amber-500/30
                @elseif(in_array($order->status, ['PROSES_LAUNDRY', 'LAUNDRY_DITERIMA', 'LAUNDRY_SELESAI', 'PENIMBANGAN'])) bg-blue-500/10 text-cyan-400 border border-blue-500/30
                @elseif(in_array($order->status, ['TAGIHAN_DIBUAT', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN'])) bg-purple-500/10 text-purple-400 border border-purple-500/30
                @elseif($order->status === 'CANCELLED') bg-rose-500/10 text-rose-400 border border-rose-500/30
                @else bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 @endif">
                STATUS: {{ str_replace('_', ' ', $order->status) }}
            </span>

            <button type="button" wire:click="openDeleteModal" class="px-4 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs rounded-xl border border-rose-500/30 transition-colors flex items-center space-x-1">
                <span>Hapus Order</span>
            </button>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Details Left -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Order Type & Pricing Summary Card -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4 text-slate-100">
                <div class="flex justify-between items-center pb-3 border-b border-slate-800">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase block">Spesifikasi Layanan</span>
                        <h3 class="font-extrabold text-white text-base">
                            {{ strtoupper($order->service_type ?? 'kiloan') }} &bull; {{ strtoupper($order->package_type ?? 'ekonomis') }} &bull; {{ strtoupper($order->speed_type ?? 'reguler') }}
                        </h3>
                    </div>
                    <span class="px-3 py-1 bg-blue-500/10 text-cyan-400 font-bold text-xs rounded-lg uppercase border border-blue-500/30">
                        Kecepatan: {{ $order->speed_type === 'ekspres' ? 'Ekspres (24 Jam)' : 'Reguler (2 Hari)' }}
                    </span>
                </div>

                <!-- Weight Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800">
                        <span class="text-slate-400 block font-semibold uppercase">Estimasi Berat Awal</span>
                        <span class="font-black text-white text-lg">±{{ number_format($order->estimated_weight ?? 0, 1, ',', '.') }} kg</span>
                    </div>

                    <div class="p-3.5 bg-blue-500/10 rounded-2xl border border-blue-500/30">
                        <span class="text-cyan-400 block font-bold uppercase">Berat Aktual (Penimbangan Akhir)</span>
                        <span class="font-black text-cyan-300 text-lg">
                            {{ $order->actual_weight ? number_format($order->actual_weight, 1, ',', '.') . ' kg' : 'Belum Ditimbang' }}
                        </span>
                    </div>
                </div>

                <!-- Custom Item Info if exists -->
                @if($order->custom_item_name)
                    <div class="p-4 bg-amber-500/10 rounded-2xl border border-amber-500/30 text-xs space-y-1">
                        <span class="font-bold text-amber-400 uppercase block">Item Lainnya (Custom Input Customer)</span>
                        <p class="font-semibold text-slate-200">Nama: {{ $order->custom_item_name }} (Qty: {{ $order->custom_item_qty }} pcs)</p>
                        @if($order->custom_item_notes) <p class="text-slate-400 italic">"{{ $order->custom_item_notes }}"</p> @endif
                    </div>
                @endif

                <!-- Item List Table -->
                <div class="space-y-2 pt-2">
                    <h4 class="font-bold text-white text-sm">Rincian Item Breakdown</h4>
                    <div class="divide-y divide-slate-800 bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                        @forelse($order->orderItems as $item)
                            <div class="py-2.5 flex justify-between items-center text-xs">
                                <div>
                                    <p class="font-bold text-slate-100">{{ $item->service_name }}</p>
                                    <span class="text-slate-400">Rp{{ number_format($item->unit_price, 0, ',', '.') }} x {{ $item->quantity }} pcs</span>
                                </div>
                                <span class="font-extrabold text-white">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 italic">Tidak ada item rincian satuan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 space-y-1.5 text-sm">
                    <div class="flex justify-between text-slate-400">
                        <span>Estimasi Harga Awal</span>
                        <span>Rp{{ number_format($order->estimated_price ?? $order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-400 font-bold">
                        <span>Ongkos Kirim (Antar-Jemput)</span>
                        <span>GRATIS (Rp 0)</span>
                    </div>
                    <div class="flex justify-between text-lg font-extrabold text-white pt-2 border-t border-slate-800">
                        <span>Total Tagihan Final</span>
                        <span class="text-cyan-400 text-xl">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Status & Proof Card -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4 text-slate-100">
                <div class="flex justify-between items-center pb-3 border-b border-slate-800">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase block">Status & Resi Pembayaran</span>
                        <h3 class="font-extrabold text-white text-base flex items-center space-x-2">
                            <span>Informasi Pembayaran Customer</span>
                        </h3>
                    </div>
                    @php
                        $isPaid = $order->payment?->status === 'PAID' || in_array($order->payment?->status, ['SUCCESS', 'SETTLEMENT']);
                    @endphp
                    @if($isPaid)
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-bold text-xs rounded-full uppercase flex items-center space-x-1">
                            <span>LUNAS</span>
                        </span>
                    @else
                        <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/30 font-bold text-xs rounded-full uppercase flex items-center space-x-1">
                            <span>BELUM BAYAR</span>
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-1">
                        <span class="text-slate-400 block font-semibold uppercase">Metode Pembayaran</span>
                        <span class="font-extrabold text-white text-sm block">{{ $order->payment?->payment_method ?? 'Belum Dipilih (Midtrans/Manual Transfer)' }}</span>
                        @if($order->payment?->payment_reference)
                            <span class="text-[10px] text-slate-400 font-mono block">Ref: {{ $order->payment->payment_reference }}</span>
                        @endif
                    </div>

                    <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-1">
                        <span class="text-slate-400 block font-semibold uppercase">Waktu & Jumlah Terbayar</span>
                        <span class="font-extrabold text-cyan-400 text-sm block">
                            Rp{{ number_format($order->payment?->amount ?? $order->total, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium block">
                            {{ $order->payment?->paid_at ? 'Dibayar pada: ' . $order->payment->paid_at->format('d M Y, H:i') . ' WIB' : 'Menunggu Pelunasan Customer' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button wire:click="openPaymentProofModal" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-2">
                        <span>Lihat & Verifikasi Bukti Pembayaran</span>
                    </button>

                    @if(!$isPaid)
                        <button wire:click="confirmPaymentAsPaid" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-2">
                            <span>Konfirmasi Lunas (Manual Admin)</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Proof Photos Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Pickup Proof -->
                <div class="bg-slate-900/90 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-2 text-slate-100">
                    <h4 class="font-bold text-white text-sm">Bukti Foto Pickup</h4>
                    @if($order->pickupProof)
                        <img src="{{ Storage::url($order->pickupProof->image_path) }}" class="w-full h-48 object-cover rounded-2xl border border-slate-800">
                        <p class="text-xs text-slate-400">Diunggah oleh: {{ $order->pickupProof->driver->name ?? 'Driver' }}</p>
                        @if($order->pickupProof->notes) <p class="text-xs italic text-slate-300">"{{ $order->pickupProof->notes }}"</p> @endif
                    @else
                        <div class="h-32 bg-slate-950/80 rounded-2xl border border-dashed border-slate-800 flex items-center justify-center text-xs text-slate-500">
                            Belum ada bukti pickup
                        </div>
                    @endif
                </div>

                <!-- Delivery Proof -->
                <div class="bg-slate-900/90 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-2 text-slate-100">
                    <h4 class="font-bold text-white text-sm">Bukti Foto Delivery</h4>
                    @if($order->deliveryProof)
                        <img src="{{ Storage::url($order->deliveryProof->image_path) }}" class="w-full h-48 object-cover rounded-2xl border border-slate-800">
                        <p class="text-xs text-slate-400">Diunggah oleh: {{ $order->deliveryProof->driver->name ?? 'Driver' }}</p>
                        @if($order->deliveryProof->notes) <p class="text-xs italic text-slate-300">"{{ $order->deliveryProof->notes }}"</p> @endif
                    @else
                        <div class="h-32 bg-slate-950/80 rounded-2xl border border-dashed border-slate-800 flex items-center justify-center text-xs text-slate-500">
                            Belum ada bukti delivery
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status History Timeline -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4 text-slate-100">
                <h3 class="font-extrabold text-white text-base">Riwayat Perubahan Status Order</h3>
                <div class="relative pl-6 border-l-2 border-slate-800 space-y-6">
                    @forelse($order->statusHistories as $history)
                        <div class="relative">
                            <div class="absolute -left-[31px] top-1 w-4 h-4 rounded-full bg-cyan-400 border-2 border-slate-900"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-slate-200 text-xs">{{ str_replace('_', ' ', $history->to_status) }}</p>
                                    <p class="text-xs text-slate-400">
                                        Oleh: <span class="font-semibold text-white">{{ $history->changed_by_role ?? 'System' }}</span>
                                        @if($history->changedBy) ({{ $history->changedBy->name }}) @endif
                                    </p>
                                    @if($history->notes) <p class="text-xs text-slate-300 mt-0.5 font-medium">"{{ $history->notes }}"</p> @endif
                                </div>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $history->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic">Belum ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Right: Operational Workflow Actions -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Action Box According to Workflow -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4 text-slate-100">
                <h3 class="font-extrabold text-white text-base flex items-center justify-between">
                    <span>Aksi Workflow Admin</span>
                    <span class="text-xs font-bold text-cyan-400 bg-blue-500/10 border border-blue-500/30 px-2.5 py-0.5 rounded-full">Proses Utama</span>
                </h3>

                @if($order->status === 'MENUNGGU_PICKUP')
                    <div class="space-y-3 p-4 bg-slate-950/80 rounded-2xl border border-amber-500/30">
                        <p class="text-xs text-slate-300 font-medium">Order baru dibuat. Silakan penugasan driver pickup atau konfirmasi penjemputan:</p>
                        <button wire:click="confirmLaundryPickedUp" class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Konfirmasi Laundry Dijemput</span>
                        </button>
                    </div>
                @elseif($order->status === 'DRIVER_DITUGASKAN')
                    <div class="space-y-3 p-4 bg-slate-950/80 rounded-2xl border border-amber-500/30">
                        <p class="text-xs text-slate-300 font-medium">Driver pickup (<strong>{{ $order->pickupDriver->name ?? 'Ditugaskan' }}</strong>) sedang menuju lokasi customer.</p>
                        <button wire:click="confirmLaundryPickedUp" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Konfirmasi Laundry Dijemput Driver</span>
                        </button>
                        <button wire:click="confirmLaundryReceived" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Konfirmasi Diterima di Outlet & Mulai Cuci</span>
                        </button>
                    </div>
                @elseif($order->status === 'LAUNDRY_DIJEMPUT')
                    <div class="space-y-3 p-4 bg-slate-950/80 rounded-2xl border border-blue-500/30">
                        <p class="text-xs text-slate-300 font-medium">Driver telah menjemput pakaian dari customer. Konfirmasi penerimaan di outlet:</p>
                        <button wire:click="confirmLaundryReceived" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Konfirmasi Laundry Diterima & Mulai Proses</span>
                        </button>
                    </div>
                @elseif($order->status === 'LAUNDRY_DITERIMA')
                    <div class="space-y-3 p-4 bg-slate-950/80 rounded-2xl border border-blue-500/30">
                        <p class="text-xs text-slate-300 font-medium">Laundry sudah diterima di outlet. Mulai proses pencucian:</p>
                        <button wire:click="startLaundryProcessing" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Mulai Proses Pencucian Laundry</span>
                        </button>
                    </div>
                @elseif(in_array($order->status, ['PROSES_LAUNDRY', 'LAUNDRY_SELESAI', 'PENIMBANGAN']))
                    <div class="space-y-3 p-4 bg-slate-950/80 rounded-2xl border border-emerald-500/30">
                        <h4 class="font-bold text-white text-xs uppercase">Penimbangan Akhir & Buat Tagihan</h4>
                        @if($order->service_type === 'kiloan')
                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Input Berat Aktual (kg)</label>
                                <input type="number" step="0.1" wire:model="inputActualWeight" placeholder="Contoh: 4.2" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 text-white rounded-xl text-sm font-bold focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('inputActualWeight') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        <button wire:click="generateInvoice" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Buat Tagihan & Hitung Harga Final</span>
                        </button>
                        <button wire:click="finishLaundryProcessing" class="w-full py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold text-xs rounded-xl transition-all">
                            Tandai Laundry Selesai Diproses
                        </button>
                    </div>
                @elseif(in_array($order->status, ['TAGIHAN_DIBUAT', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN', 'PEMBAYARAN_DRIVER', 'ORDER_SELESAI']))
                    <div class="space-y-3 p-4 bg-slate-950/80 rounded-2xl border border-purple-500/30">
                        <h4 class="font-bold text-purple-400 text-xs uppercase">Aksi Pengiriman & Resi</h4>
                        
                        @if($order->status === 'TAGIHAN_DIBUAT')
                            <p class="text-xs text-slate-300 font-medium">Tagihan dibuat. Tugaskan driver delivery atau konfirmasi pengembalian:</p>
                            <button wire:click="confirmDeliveryDone" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                                <span>Konfirmasi Laundry Dikembalikan Customer</span>
                            </button>
                        @elseif($order->status === 'DRIVER_PENGIRIMAN_DITUGASKAN')
                            <p class="text-xs text-slate-300 font-medium">Driver delivery (<strong>{{ $order->deliveryDriver->name ?? 'Ditugaskan' }}</strong>) sedang mengantar laundry.</p>
                            <button wire:click="confirmDeliveryDone" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                                <span>Konfirmasi Laundry Diterima Customer</span>
                            </button>
                        @elseif($order->status === 'LAUNDRY_DIKEMBALIKAN')
                            <p class="text-xs text-slate-300 font-medium">Laundry telah diterima customer. Konfirmasi setoran driver / selesaikan order:</p>
                            <button wire:click="confirmDriverPayment" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                                <span>Konfirmasi Setoran / Pembayaran Driver</span>
                            </button>
                            <button wire:click="completeOrder" class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                                <span>Selesaikan Order (Completed)</span>
                            </button>
                        @elseif($order->status === 'PEMBAYARAN_DRIVER')
                            <p class="text-xs text-slate-300 font-medium">Pembayaran driver dikonfirmasi. Selesaikan order:</p>
                            <button wire:click="completeOrder" class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                                <span>Selesaikan Order (Completed)</span>
                            </button>
                        @elseif($order->status === 'ORDER_SELESAI')
                            <p class="text-xs text-emerald-400 font-bold text-center">Order Ini Sudah Selesai Sepenuhnya.</p>
                        @endif

                        <button wire:click="markReceiptPrinted" class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Cetak Resi Pembayaran</span>
                        </button>
                        @if($order->receipt_printed_at)
                            <p class="text-[11px] text-purple-300 font-semibold text-center">Resi dicetak pada: {{ $order->receipt_printed_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-slate-500 italic">Tidak ada aksi utama untuk status saat ini.</p>
                @endif
            </div>

            <!-- Manual Status Update Card (Admin Override) -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-3 text-slate-100">
                <h3 class="font-extrabold text-white text-sm flex items-center justify-between">
                    <span>Update Status Manual (Admin)</span>
                    <span class="text-[10px] bg-slate-800 text-slate-400 font-bold px-2 py-0.5 rounded-md border border-slate-700">Override</span>
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Pilih Status Baru</label>
                        <select wire:model="manualStatus" class="w-full px-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">-- Pilih Status Order --</option>
                            @foreach($statusLabels as $statusKey => $label)
                                <option value="{{ $statusKey }}" @if($order->status === $statusKey) selected @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('manualStatus') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Catatan Perubahan (Opsional)</label>
                        <input type="text" wire:model="manualNotes" placeholder="Contoh: Update manual oleh Admin..." class="w-full px-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-medium text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button wire:click="updateStatusManual" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-md transition-all border border-slate-700">
                        Update Status Sekarang
                    </button>
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-3 text-slate-100">
                <h3 class="font-extrabold text-white text-base">Informasi Pelanggan</h3>
                <div class="text-xs space-y-2 text-slate-300">
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Nama</span>
                        <p class="font-bold text-sm text-white">{{ $order->customer->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Nomor HP / WhatsApp</span>
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $order->pickup_phone ?? $order->customer->phone ?? '');
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            $waText = urlencode("Halo " . ($order->customer->name ?? 'Pelanggan') . ", mengenai pesanan laundry {$order->order_number} (Status: " . str_replace('_', ' ', $order->status) . ").");
                            $waUrl = "https://wa.me/{$cleanPhone}?text={$waText}";
                        @endphp
                        <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center space-x-1.5 font-bold text-emerald-400 hover:text-white bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/30 mt-1 transition-all">
                            <span>{{ $order->pickup_phone }}</span>
                            <span>Chat WhatsApp</span>
                        </a>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Alamat Pickup</span>
                        <p class="font-medium bg-slate-950/80 p-2.5 rounded-xl border border-slate-800 mt-1">{{ $order->pickup_address }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Alamat Delivery</span>
                        <p class="font-medium bg-slate-950/80 p-2.5 rounded-xl border border-slate-800 mt-1">{{ $order->delivery_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Driver Pickup Assignment Box -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-3 text-slate-100">
                <div class="flex justify-between items-center">
                    <h3 class="font-extrabold text-white text-sm">Penugasan Driver Pickup</h3>
                    @if($order->pickupDriver)
                        <span class="text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                            Aktif: {{ $order->pickupDriver->name }}
                        </span>
                    @endif
                </div>
                <div class="space-y-3">
                    <select wire:model="selectedPickupDriverId" class="w-full px-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Belum Ada Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                        @endforeach
                    </select>
                    @error('selectedPickupDriverId') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    <button wire:click="assignPickupDriver" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                        Assign Pickup Driver
                    </button>
                </div>
            </div>

            <!-- Driver Delivery Assignment Box -->
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-3 text-slate-100">
                <div class="flex justify-between items-center">
                    <h3 class="font-extrabold text-white text-sm">Penugasan Driver Delivery</h3>
                    @if($order->deliveryDriver)
                        <span class="text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30 px-2 py-0.5 rounded-full">
                            Aktif: {{ $order->deliveryDriver->name }}
                        </span>
                    @endif
                </div>
                <div class="space-y-3">
                    <select wire:model="selectedDeliveryDriverId" class="w-full px-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Belum Ada Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                        @endforeach
                    </select>
                    @error('selectedDeliveryDriverId') <span class="text-[11px] text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    <button wire:click="assignDeliveryDriver" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                        Assign Delivery Driver
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Printable Modal -->
    @if($showReceiptModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-slate-900 rounded-3xl p-8 max-w-lg w-full shadow-2xl space-y-6 relative border border-slate-800 text-slate-100">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-lg font-black text-white">RESI PEMBAYARAN WASHLY</h2>
                        <p class="text-xs text-slate-400 font-mono">No. Order: {{ $order->order_number }}</p>
                    </div>
                    <button wire:click="$set('showReceiptModal', false)" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                </div>

                <!-- Printable Receipt Area -->
                <div id="receipt-print-area" class="space-y-4 text-xs bg-slate-950/90 p-6 rounded-2xl border border-slate-800 font-sans text-slate-100">
                    <div class="text-center pb-4 border-b border-dashed border-slate-800 space-y-1">
                        <h3 class="font-black text-2xl text-cyan-400 tracking-tight">WASHLY LAUNDRY</h3>
                        <p class="text-slate-300 text-xs font-semibold">Layanan Premium Antar - Jemput Laundry</p>
                        <p class="text-[10px] text-slate-500 font-mono">Tgl Cetak: {{ now()->format('d M Y, H:i') }} WIB</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-slate-300 py-2">
                        <div>
                            <span class="text-slate-500 block font-bold text-[10px] uppercase">Pelanggan</span>
                            <span class="font-bold text-white">{{ $order->customer->name }}</span>
                            <span class="block text-slate-400">{{ $order->pickup_phone }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 block font-bold text-[10px] uppercase">Spesifikasi</span>
                            <span class="font-bold text-white uppercase">{{ $order->service_type }} &bull; {{ $order->package_type }}</span>
                            <span class="block text-cyan-400 font-bold uppercase">{{ $order->speed_type === 'ekspres' ? 'Ekspres (24 Jam)' : 'Reguler (2 Hari)' }}</span>
                        </div>
                    </div>

                    @if($order->actual_weight)
                        <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/30 text-cyan-300 font-bold flex justify-between">
                            <span>Berat Final Penimbangan:</span>
                            <span>{{ number_format($order->actual_weight, 1, ',', '.') }} kg</span>
                        </div>
                    @endif

                    <div class="py-3 border-t border-b border-dashed border-slate-800 space-y-2">
                        <div class="flex justify-between font-bold text-slate-500 text-[10px] uppercase">
                            <span>Item Rincian</span>
                            <span>Subtotal</span>
                        </div>
                        @forelse($order->orderItems as $item)
                            <div class="flex justify-between text-slate-200 font-medium">
                                <span>{{ $item->service_name }} ({{ $item->quantity }}x)</span>
                                <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div class="flex justify-between text-slate-200 font-medium">
                                <span>Laundry {{ ucfirst($order->service_type) }}</span>
                                <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforelse
                    </div>

                    <div class="space-y-1 pt-1 font-bold">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal:</span>
                            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-400">
                            <span>Ongkir:</span>
                            <span>GRATIS</span>
                        </div>
                        <div class="flex justify-between text-base text-white pt-2 border-t border-slate-800">
                            <span>TOTAL:</span>
                            <span class="text-cyan-400">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-center pt-4 text-[11px] text-slate-500 italic">
                        Terima kasih telah mempercayakan laundry Anda kepada Washly!
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg flex items-center space-x-2">
                        <span>Print / Cetak Resi</span>
                    </button>
                    <button wire:click="$set('showReceiptModal', false)" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-rose-900/50 text-slate-100">
                <div class="flex items-center space-x-3 text-rose-400">
                    <div class="p-3 bg-rose-500/10 rounded-2xl border border-rose-500/30 font-bold text-xs">
                        HAPUS
                    </div>
                    <div>
                        <h3 class="font-extrabold text-white text-lg">Konfirmasi Hapus Order</h3>
                        <p class="text-xs text-slate-400">Order No: <strong class="font-mono text-cyan-400">{{ $order->order_number }}</strong></p>
                    </div>
                </div>

                <div class="p-4 bg-rose-950/60 rounded-2xl border border-rose-800/80 text-xs text-rose-300 font-medium">
                    Apakah Anda yakin ingin menghapus data order ini secara permanen? Seluruh rincian item, riwayat status, dan bukti pengiriman terkait akan ikut dibersihkan.
                </div>

                <div class="flex justify-end space-x-3 pt-2 border-t border-slate-800">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteOrder" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-lg">
                        Ya, Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Proof Inspector Modal -->
    @if($showPaymentProofModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl space-y-6 relative border border-slate-800 max-h-[90vh] overflow-y-auto text-slate-100">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-lg font-black text-white">Bukti Pembayaran Order #{{ $order->order_number }}</h2>
                        <p class="text-xs text-slate-400">Verifikasi status transaksi digital & foto transfer</p>
                    </div>
                    <button wire:click="closePaymentProofModal" class="text-slate-400 hover:text-white font-bold text-lg">✕</button>
                </div>

                <!-- Status Overview inside Modal -->
                <div class="p-4 rounded-2xl border text-xs space-y-2
                    @if($order->payment?->status === 'PAID' || in_array($order->payment?->status, ['SUCCESS', 'SETTLEMENT'])) bg-emerald-500/10 border-emerald-500/30 text-emerald-300
                    @else bg-amber-500/10 border-amber-500/30 text-amber-300 @endif">
                    <div class="flex justify-between items-center">
                        <span class="font-bold uppercase">Status Transaksi:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black
                            @if($order->payment?->status === 'PAID' || in_array($order->payment?->status, ['SUCCESS', 'SETTLEMENT'])) bg-emerald-600 text-white
                            @else bg-amber-600 text-white @endif">
                            {{ $order->payment?->status ?? 'PENDING' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-400 font-medium block">Nominal Tagihan:</span>
                            <span class="font-extrabold text-sm text-cyan-400">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Metode:</span>
                            <span class="font-extrabold text-sm text-white">{{ $order->payment?->payment_method ?? 'Midtrans Sandbox / Bank' }}</span>
                        </div>
                    </div>
                    @if($order->payment?->payment_reference)
                        <div class="pt-1 text-[11px] font-mono text-slate-400">
                            Reference / Order ID: <strong class="text-cyan-400">{{ $order->payment->payment_reference }}</strong>
                        </div>
                    @endif
                </div>

                <!-- Digital Payment Gateway Info (Midtrans / Sandbox / QRIS) -->
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-2 text-xs">
                    <h4 class="font-extrabold text-white flex items-center justify-between">
                        <span>Informasi Digital Gateway (Midtrans Sandbox / QRIS)</span>
                        <span class="text-[10px] font-bold text-cyan-400 bg-blue-500/20 px-2 py-0.5 rounded-md border border-blue-500/30">Gateway API</span>
                    </h4>
                    @if($order->payment)
                        <div class="space-y-1 text-slate-300">
                            <div class="flex justify-between">
                                <span>Status Gateway:</span>
                                <span class="font-bold text-white">{{ strtoupper($order->payment->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Waktu Transaksi:</span>
                                <span class="font-bold text-white">{{ $order->payment->updated_at?->format('d F Y, H:i') }} WIB</span>
                            </div>
                            <div class="flex justify-between">
                                <span>ID Transaksi:</span>
                                <span class="font-mono text-cyan-400">{{ $order->payment->payment_reference }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-slate-500 italic">Belum ada catatan transaksi digital gateway untuk order ini.</p>
                    @endif
                </div>

                <!-- Transfer Receipt Photo (Bukti Transfer Manual) -->
                <div class="space-y-3">
                    <h4 class="font-extrabold text-white text-xs uppercase tracking-wide">Foto Bukti Transfer Pelanggan</h4>
                    @if($order->payment?->proof_image)
                        <div class="space-y-2">
                            <a href="{{ Storage::url($order->payment->proof_image) }}" target="_blank" class="block relative group">
                                <img src="{{ Storage::url($order->payment->proof_image) }}" alt="Bukti Pembayaran" class="w-full max-h-72 object-contain rounded-2xl border border-slate-800 bg-slate-950 shadow-sm">
                                <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center text-white text-xs font-bold space-x-1">
                                    <span>Klik untuk Perbesar</span>
                                </div>
                            </a>
                            <p class="text-[11px] text-slate-500 text-center">Tersimpan di: <code class="font-mono text-slate-400">{{ $order->payment->proof_image }}</code></p>
                        </div>
                    @else
                        <div class="p-6 bg-slate-950/80 rounded-2xl border border-dashed border-slate-800 text-center space-y-2">
                            <p class="text-xs text-slate-500 font-medium">Belum ada foto bukti transfer yang diunggah.</p>
                        </div>
                    @endif

                    <!-- Form Upload Bukti Foto (Oleh Admin / Kasir) -->
                    <div class="p-4 bg-blue-500/10 rounded-2xl border border-blue-500/30 space-y-3">
                        <label class="block text-xs font-bold text-cyan-300 uppercase">Upload / Ganti Bukti Pembayaran Manual</label>
                        <input type="file" wire:model="uploadPaymentProofPhoto" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                        @error('uploadPaymentProofPhoto') <span class="text-[11px] text-rose-400 font-semibold block">{{ $message }}</span> @enderror
                        
                        <div wire:loading wire:target="uploadPaymentProofPhoto" class="text-xs text-cyan-400 font-bold">
                            Mengunggah gambar...
                        </div>

                        @if($uploadPaymentProofPhoto)
                            <div class="pt-2">
                                <button type="button" wire:click="uploadPaymentProofPhoto" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all">
                                    Simpan Foto & Verifikasi LUNAS
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-slate-800">
                    @if(!($order->payment?->status === 'PAID' || in_array($order->payment?->status, ['SUCCESS', 'SETTLEMENT'])))
                        <button wire:click="confirmPaymentAsPaid" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Tandai Lunas (Manual Admin)</span>
                        </button>
                    @else
                        <span class="text-xs text-emerald-400 font-bold flex items-center space-x-1">
                            <span>Transaksi Terverifikasi LUNAS</span>
                        </span>
                    @endif

                    <button wire:click="closePaymentProofModal" class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
