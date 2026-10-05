<div class="space-y-8 max-w-3xl mx-auto">
    <div class="flex items-center space-x-3">
        <a href="{{ route('customer.dashboard') }}" class="p-2 bg-slate-900 rounded-xl border border-slate-800 text-slate-400 hover:text-white transition-colors">
            &larr;
        </a>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Tracking Order {{ $order->order_number }}</h1>
            <p class="text-xs text-slate-400">Lacak perkembangan pencucian dan penjemputan secara realtime.</p>
        </div>
    </div>

    <!-- Flash Alerts -->
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

    <!-- Status Header Banner -->
    <div class="@if($order->status === 'CANCELLED') bg-rose-950/80 border-rose-800/80 @elseif($order->status === 'ORDER_SELESAI' || $order->status === 'COMPLETED') bg-emerald-950/80 border-emerald-800/80 @else bg-slate-900/90 border-blue-500/30 @endif border rounded-3xl p-6 text-white shadow-2xl flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Status Terkini</span>
            <p class="text-2xl font-black text-cyan-400 mt-1">{{ str_replace('_', ' ', $order->status) }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @php
                $canPay = in_array($order->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']) && (!$order->payment || $order->payment->status !== 'PAID');
            @endphp
            @if($canPay)
                <button type="button" wire:click="openPaymentModal" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-1.5">
                    <span>Pembayaran Laundry</span>
                </button>
            @elseif($order->payment && $order->payment->status === 'PAID')
                <span class="px-3.5 py-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-extrabold text-xs rounded-xl">
                    LUNAS
                </span>
            @endif
            <button type="button" wire:click="openReceiptModal" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-xs rounded-xl transition-all flex items-center space-x-1.5 border border-slate-700">
                <span>Cetak Resi</span>
            </button>
        </div>
    </div>

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

    <!-- Driver Info Cards if Assigned -->
    @if($order->pickupDriver || $order->deliveryDriver || $order->pickupProof || $order->deliveryProof)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($order->pickupDriver || $order->pickupProof)
                <div class="bg-slate-900/90 rounded-2xl p-4 border border-blue-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold text-cyan-400 uppercase tracking-wider block">Driver Pickup</span>
                            <p class="font-extrabold text-white text-sm mt-0.5">{{ $order->pickupDriver->name ?? 'Driver Ditugaskan' }}</p>
                            @if($order->pickupDriver?->phone)
                                <p class="text-xs text-slate-400">{{ $order->pickupDriver->phone }}</p>
                            @endif
                        </div>
                        @if($order->pickupDriver?->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->pickupDriver->phone) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center space-x-1">
                                <span>Hubungi WA</span>
                            </a>
                        @endif
                    </div>

                    @if($order->pickupProof)
                        <div class="pt-2 border-t border-slate-800 space-y-2">
                            <span class="text-xs font-extrabold text-emerald-400 flex items-center gap-1">
                                <span>Bukti Foto Penjemputan Driver</span>
                            </span>
                            <img src="{{ Storage::url($order->pickupProof->image_path) }}" class="w-full h-36 object-cover rounded-xl border border-slate-800 shadow-sm">
                            <p class="text-[10px] text-slate-500 font-mono">Dikonfirmasi: {{ $order->pickupProof->created_at->format('d M Y, H:i') }} WIB</p>
                            @if($order->pickupProof->notes)
                                <p class="text-xs text-slate-300 italic bg-slate-950/80 p-2 rounded-lg border border-slate-800">"{{ $order->pickupProof->notes }}"</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @if($order->deliveryDriver || $order->deliveryProof)
                <div class="bg-slate-900/90 rounded-2xl p-4 border border-indigo-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold text-indigo-400 uppercase tracking-wider block">Driver Delivery</span>
                            <p class="font-extrabold text-white text-sm mt-0.5">{{ $order->deliveryDriver->name ?? 'Driver Ditugaskan' }}</p>
                            @if($order->deliveryDriver?->phone)
                                <p class="text-xs text-slate-400">{{ $order->deliveryDriver->phone }}</p>
                            @endif
                        </div>
                        @if($order->deliveryDriver?->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->deliveryDriver->phone) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center space-x-1">
                                <span>Hubungi WA</span>
                            </a>
                        @endif
                    </div>

                    @if($order->deliveryProof)
                        <div class="pt-2 border-t border-slate-800 space-y-2">
                            <span class="text-xs font-extrabold text-emerald-400 flex items-center gap-1">
                                <span>Bukti Foto Pengantaran Driver</span>
                            </span>
                            <img src="{{ Storage::url($order->deliveryProof->image_path) }}" class="w-full h-36 object-cover rounded-xl border border-slate-800 shadow-sm">
                            <p class="text-[10px] text-slate-500 font-mono">Dikonfirmasi: {{ $order->deliveryProof->created_at->format('d M Y, H:i') }} WIB</p>
                            @if($order->deliveryProof->notes)
                                <p class="text-xs text-slate-300 italic bg-slate-950/80 p-2 rounded-lg border border-slate-800">"{{ $order->deliveryProof->notes }}"</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Visual Stepper Timeline -->
    <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl space-y-6">
        <h3 class="font-extrabold text-white text-lg border-b border-slate-800 pb-3">Timeline Progress Laundry</h3>

        <div class="relative pl-8 border-l-4 border-slate-800 space-y-8">
            @foreach($timelineStages as $index => $stage)
                @php
                    $isPassed = ($order->status !== 'CANCELLED') && ($index <= $currentStageIndex);
                    $isCurrent = ($order->status !== 'CANCELLED') && ($index === $currentStageIndex);

                    // Find matching history record for this timeline step
                    $historyMatch = $order->statusHistories
                        ->filter(fn($h) => in_array($h->to_status, $stage['aliases'], true) || $h->to_status === $stage['key'])
                        ->last();
                @endphp

                <div class="relative">
                    <!-- Circle node -->
                    <div class="absolute -left-[44px] top-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all
                        {{ $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-500/20 shadow-lg' : ($isPassed ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-500') }}">
                        {{ $index + 1 }}
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm {{ $isPassed ? 'text-white' : 'text-slate-500' }}">
                                {{ $stage['label'] }}
                            </h4>
                            @if($historyMatch)
                                <span class="text-[11px] font-mono text-slate-500">{{ $historyMatch->created_at->format('H:i, d M') }}</span>
                            @endif
                        </div>

                        <p class="text-xs {{ $isPassed ? 'text-slate-300' : 'text-slate-600' }}">
                            {{ $stage['desc'] }}
                        </p>

                        @if($isCurrent)
                            <div class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-cyan-400 border border-blue-500/30 animate-pulse">
                                ● SEMENTARA DIPROSES
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
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
