<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('customer.orders.index') }}" class="p-2 bg-slate-900 rounded-xl border border-slate-800 text-slate-400 hover:text-white transition-colors">
                &larr;
            </a>
            <h1 class="text-2xl font-black text-white tracking-tight">Detail Order {{ $order->order_number }}</h1>
        </div>
        <div class="flex items-center space-x-2">
            @php
                $canPay = in_array($order->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']) && (!$order->payment || $order->payment->status !== 'PAID');
            @endphp
            @if($canPay)
                <button wire:click="openPaymentModal" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-1.5">
                    <span>Pembayaran Laundry</span>
                </button>
            @elseif($order->payment && $order->payment->status === 'PAID')
                <span class="px-3.5 py-2 bg-emerald-500/10 text-emerald-400 font-extrabold text-xs rounded-xl border border-emerald-500/30">
                    LUNAS
                </span>
            @endif
            <button wire:click="openReceiptModal" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-xs rounded-xl transition-all flex items-center space-x-1.5 border border-slate-700">
                <span>Cetak Resi</span>
            </button>
            <a href="{{ route('customer.tracking', $order->id) }}" class="px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-cyan-400 font-bold text-xs rounded-xl border border-blue-500/30">
                Tracking
            </a>
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

    <!-- Order Completion Action Card for Customer -->
    @if(in_array($order->status, ['LAUNDRY_DIKEMBALIKAN', 'PEMBAYARAN_DRIVER', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'TAGIHAN_DIBUAT']))
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-emerald-500/30 shadow-xl space-y-3">
            <div class="flex items-center space-x-3">
                <div>
                    <h3 class="font-extrabold text-white text-sm">Pakaian Anda Sudah Diterima?</h3>
                    <p class="text-xs text-slate-400">Klik tombol di bawah untuk mengonfirmasi bahwa pakaian telah sampai dan pesanan selesai.</p>
                </div>
            </div>
            <button wire:click="confirmOrderCompleted" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                <span>Konfirmasi Pesanan Selesai</span>
            </button>
        </div>
    @elseif(in_array($order->status, ['ORDER_SELESAI', 'COMPLETED']))
        <div class="bg-emerald-950/60 rounded-3xl p-5 border border-emerald-800/80 text-xs text-emerald-300 space-y-1">
            <div class="flex items-center space-x-2 font-black">
                <span>Pesanan Berhasil Dikonfirmasi Selesai</span>
            </div>
            <p class="text-slate-400">Pesanan ini akan tersimpan di riwayat Anda selama 24 jam sebelum diarsipkan secara otomatis.</p>
            @if($order->completed_at)
                <p class="text-[11px] text-emerald-400 font-mono pt-1">Dikonfirmasi pada: {{ $order->completed_at->format('d M Y, H:i') }} WIB</p>
            @endif
        </div>
    @endif

    <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl space-y-6">
        <div class="flex justify-between items-center pb-4 border-b border-slate-800">
            <div>
                <span class="text-xs text-slate-400 block font-mono">Dibuat: {{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold
                @if(in_array($order->status, ['ORDER_SELESAI', 'COMPLETED'])) bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                @else bg-blue-500/10 text-cyan-400 border border-blue-500/30 @endif">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </div>

        <!-- Items Table -->
        <div>
            <h3 class="font-extrabold text-white text-base mb-3">Item Laundry</h3>
            <div class="bg-slate-950/80 rounded-2xl p-4 border border-slate-800 divide-y divide-slate-800/60">
                @foreach($order->orderItems as $item)
                    <div class="py-2.5 flex justify-between items-center text-xs">
                        <div>
                            <span class="font-bold text-slate-200 text-sm">{{ $item->service_name }}</span>
                            <span class="text-slate-400 block font-mono">Rp{{ number_format($item->unit_price, 0, ',', '.') }} x {{ $item->quantity }} pcs</span>
                        </div>
                        <span class="font-black text-white text-sm">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Totals -->
        <div class="p-4 bg-slate-950/80 rounded-2xl space-y-2 text-xs border border-slate-800">
            <div class="flex justify-between text-slate-300">
                <span>Subtotal</span>
                <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-emerald-400 font-bold">
                <span>Ongkir Antar-Jemput</span>
                <span>GRATIS</span>
            </div>
            <div class="pt-2 border-t border-slate-800 flex justify-between font-black text-white text-base">
                <span>TOTAL</span>
                <span class="text-cyan-400">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Driver Confirmation & Proof Photos -->
        @if($order->pickupProof || $order->deliveryProof || $order->pickupDriver || $order->deliveryDriver)
            <div class="space-y-4 pt-4 border-t border-slate-800">
                <h3 class="font-extrabold text-white text-base">Informasi & Bukti Driver</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Pickup Proof Card -->
                    <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-cyan-400 uppercase">Driver Penjemputan</span>
                            @if($order->pickupDriver)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->pickupDriver->phone) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-[10px]">
                                    Chat Driver WA
                                </a>
                            @endif
                        </div>
                        @if($order->pickupDriver)
                            <p class="font-bold text-slate-200">{{ $order->pickupDriver->name }} ({{ $order->pickupDriver->phone }})</p>
                        @else
                            <p class="text-slate-500 italic">Belum ditugaskan</p>
                        @endif

                        @if($order->pickupProof)
                            <div class="pt-2 border-t border-slate-800 space-y-2">
                                <span class="font-bold text-emerald-400 block">Bukti Foto Penjemputan:</span>
                                <img src="{{ Storage::url($order->pickupProof->image_path) }}" class="w-full h-36 object-cover rounded-xl border border-slate-800 shadow-sm">
                                <p class="text-[11px] text-slate-500 font-mono">Dikonfirmasi: {{ $order->pickupProof->created_at->format('d M Y, H:i') }} WIB</p>
                                @if($order->pickupProof->notes)
                                    <p class="text-slate-400 italic">"{{ $order->pickupProof->notes }}"</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Delivery Proof Card -->
                    <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-indigo-400 uppercase">Driver Pengantaran</span>
                            @if($order->deliveryDriver)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->deliveryDriver->phone) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-[10px]">
                                    Chat Driver WA
                                </a>
                            @endif
                        </div>
                        @if($order->deliveryDriver)
                            <p class="font-bold text-slate-200">{{ $order->deliveryDriver->name }} ({{ $order->deliveryDriver->phone }})</p>
                        @else
                            <p class="text-slate-500 italic">Belum ditugaskan</p>
                        @endif

                        @if($order->deliveryProof)
                            <div class="pt-2 border-t border-slate-800 space-y-2">
                                <span class="font-bold text-emerald-400 block">Bukti Foto Pengantaran:</span>
                                <img src="{{ Storage::url($order->deliveryProof->image_path) }}" class="w-full h-36 object-cover rounded-xl border border-slate-800 shadow-sm">
                                <p class="text-[11px] text-slate-500 font-mono">Dikonfirmasi: {{ $order->deliveryProof->created_at->format('d M Y, H:i') }} WIB</p>
                                @if($order->deliveryProof->notes)
                                    <p class="text-slate-400 italic">"{{ $order->deliveryProof->notes }}"</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Addresses -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800">
                <span class="font-extrabold text-cyan-400 block mb-1">Lokasi Pickup</span>
                <p class="font-bold text-slate-200">{{ $order->pickup_name }} ({{ $order->pickup_phone }})</p>
                <p class="text-slate-400 mt-1">{{ $order->pickup_address }}</p>
            </div>
            <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800">
                <span class="font-extrabold text-indigo-400 block mb-1">Lokasi Delivery</span>
                <p class="font-bold text-slate-200">{{ $order->delivery_name }} ({{ $order->delivery_phone }})</p>
                <p class="text-slate-400 mt-1">{{ $order->delivery_address }}</p>
            </div>
        </div>
    </div>

    <!-- Receipt Printable Modal for Customer -->
    @if($showReceiptModal && ($order ?? $selectedReceiptOrder))
        @php
            $receiptObj = $order ?? $selectedReceiptOrder;
        @endphp
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 relative border border-slate-800 text-slate-100">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-lg font-black text-white">RESI PEMBAYARAN WASHLY</h2>
                        <p class="text-xs text-slate-400 font-mono">No. Order: {{ $receiptObj->order_number }}</p>
                    </div>
                    <button wire:click="closeReceiptModal" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                </div>

                <!-- Printable Receipt Area -->
                <div id="receipt-print-area" class="space-y-4 text-xs bg-slate-950/90 p-5 rounded-2xl border border-slate-800 font-sans text-slate-100">
                    <!-- Header -->
                    <div class="text-center pb-3 border-b border-dashed border-slate-800 space-y-1">
                        <h3 class="font-black text-xl text-cyan-400 tracking-tight">WASHLY LAUNDRY</h3>
                        <p class="text-slate-300 text-xs font-semibold">Layanan Premium Antar - Jemput Laundry</p>
                        <p class="text-[10px] text-slate-500 font-mono">Tgl Cetak: {{ now()->format('d M Y, H:i') }} WIB</p>
                    </div>

                    <!-- Customer & Order Info -->
                    <div class="grid grid-cols-2 gap-2 text-slate-300 py-1 text-[11px]">
                        <div>
                            <span class="text-slate-500 block font-bold text-[10px] uppercase">Pelanggan</span>
                            <span class="font-bold text-white">{{ $receiptObj->customer->name ?? $receiptObj->pickup_name }}</span>
                            <span class="block text-slate-400">{{ $receiptObj->pickup_phone }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 block font-bold text-[10px] uppercase">Spesifikasi</span>
                            <span class="font-bold text-white uppercase">{{ $receiptObj->service_type }} &bull; {{ $receiptObj->package_type ?? 'satuan' }}</span>
                            <span class="block text-cyan-400 font-bold uppercase">{{ $receiptObj->speed_type === 'ekspres' ? 'Ekspres (24 Jam)' : 'Reguler (2 Hari)' }}</span>
                        </div>
                    </div>

                    @if($receiptObj->actual_weight)
                        <div class="p-2.5 bg-blue-500/10 rounded-xl border border-blue-500/30 text-cyan-300 font-bold flex justify-between text-xs">
                            <span>Berat Final Penimbangan:</span>
                            <span>{{ number_format($receiptObj->actual_weight, 1, ',', '.') }} kg</span>
                        </div>
                    @endif

                    <!-- Items Breakdown -->
                    <div class="py-2 border-t border-b border-dashed border-slate-800 space-y-1.5">
                        <div class="flex justify-between font-bold text-slate-500 text-[10px] uppercase pb-1">
                            <span>Item Rincian</span>
                            <span>Subtotal</span>
                        </div>
                        @forelse($receiptObj->orderItems as $item)
                            <div class="flex justify-between text-slate-200 font-medium">
                                <span>{{ $item->service_name }} ({{ $item->quantity }}x)</span>
                                <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div class="flex justify-between text-slate-200 font-medium">
                                <span>Laundry {{ ucfirst($receiptObj->service_type) }}</span>
                                <span>Rp{{ number_format($receiptObj->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforelse
                    </div>

                    <!-- Price Totals -->
                    <div class="space-y-1 pt-1 font-bold text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal:</span>
                            <span>Rp{{ number_format($receiptObj->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-400">
                            <span>Ongkir Antar-Jemput:</span>
                            <span>GRATIS</span>
                        </div>
                        <div class="flex justify-between text-base font-black text-white pt-2 border-t border-slate-800">
                            <span>TOTAL BAYAR:</span>
                            <span class="text-cyan-400">Rp{{ number_format($receiptObj->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Status Banner -->
                    <div class="text-center pt-2">
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider
                            @if(in_array($receiptObj->status, ['ORDER_SELESAI', 'COMPLETED', 'PEMBAYARAN_DRIVER'])) bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                            @else bg-blue-500/10 text-cyan-400 border border-blue-500/30 @endif">
                            STATUS: {{ str_replace('_', ' ', $receiptObj->status) }}
                        </span>
                    </div>
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-2 pt-2 border-t border-slate-800">
                    <button type="button" wire:click="closeReceiptModal" class="w-full sm:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Tutup
                    </button>
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <button type="button" onclick="downloadReceiptAsJpg('{{ $receiptObj->order_number }}')" class="flex-1 sm:flex-none px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-1.5">
                            <span>Download Resi (JPG)</span>
                        </button>
                        <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                            Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Gateway Modal -->
    @include('livewire.customer.payment-gateway-modal')
</div>

