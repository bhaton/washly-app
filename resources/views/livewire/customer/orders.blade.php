<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Riwayat Pesanan Saya</h1>
            <p class="text-sm text-slate-400">Daftar pesanan aktif & riwayat pesanan (disimpan selama 24 jam setelah selesai).</p>
        </div>
        <a href="{{ route('customer.orders.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-500/25 transition-all">
            + Buat Pesanan Baru
        </a>
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

    <div class="p-4 bg-blue-500/10 rounded-2xl border border-blue-500/30 text-xs text-cyan-300 flex items-center space-x-2 font-medium">
        <span><strong>Catatan:</strong> Pesanan yang sudah dikonfirmasi selesai akan tampil di riwayat selama 24 jam sebelum diarsipkan secara otomatis.</span>
    </div>

    <div class="bg-slate-900/90 rounded-3xl p-4 sm:p-6 border border-slate-800 shadow-xl flex flex-col md:flex-row gap-4 justify-between">
        <div class="w-full md:w-1/2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor order..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
        </div>
        <div class="w-full md:w-1/3">
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                <option value="">Semua Status</option>
                <option value="MENUNGGU_PICKUP">Menunggu Pickup</option>
                <option value="DRIVER_DITUGASKAN">Driver Ditugaskan</option>
                <option value="PROSES_LAUNDRY">Proses Laundry</option>
                <option value="TAGIHAN_DIBUAT">Tagihan Dibuat</option>
                <option value="LAUNDRY_DIKEMBALIKAN">Laundry Dikembalikan</option>
                <option value="ORDER_SELESAI">Order Selesai</option>
            </select>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($orders as $order)
            <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                    <div>
                        <span class="font-mono font-extrabold text-cyan-400 text-sm">{{ $order->order_number }}</span>
                        <span class="text-xs text-slate-500 block sm:inline sm:ml-2 font-mono">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold w-max
                        @if(in_array($order->status, ['ORDER_SELESAI', 'COMPLETED'])) bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                        @elseif(in_array($order->status, ['MENUNGGU_PICKUP', 'DRIVER_DITUGASKAN'])) bg-amber-500/10 text-amber-400 border border-amber-500/30
                        @else bg-blue-500/10 text-cyan-400 border border-blue-500/30 @endif">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                    <div>
                        <p class="font-bold text-slate-200">{{ $order->orderItems->sum('quantity') ?: 1 }} item laundry ({{ strtoupper($order->service_type ?? 'kiloan') }})</p>
                        <p class="text-slate-400 mt-0.5">Subtotal: Rp{{ number_format($order->subtotal, 0, ',', '.') }} | Ongkir: GRATIS</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Total Pembayaran</span>
                        <span class="text-lg font-black text-cyan-400">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2">
                    <div class="text-[11px] text-slate-400 font-mono">
                        @if(in_array($order->status, ['ORDER_SELESAI', 'COMPLETED']))
                            <span class="text-emerald-400 font-bold">Selesai</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $canPay = in_array($order->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']) && (!$order->payment || $order->payment->status !== 'PAID');
                        @endphp
                        
                        @if($canPay)
                            <button type="button" wire:click="openPaymentModal({{ $order->id }})" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-1">
                                <span>Bayar Sekarang</span>
                            </button>
                        @elseif(in_array($order->status, ['LAUNDRY_DIKEMBALIKAN', 'PEMBAYARAN_DRIVER']))
                            <button wire:click="confirmOrderCompleted({{ $order->id }})" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all">
                                Konfirmasi Selesai
                            </button>
                        @endif

                        @if($order->payment && $order->payment->status === 'PAID')
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 font-extrabold text-xs rounded-xl border border-emerald-500/30">
                                LUNAS
                            </span>
                        @endif

                        <button type="button" wire:click="openReceiptModal({{ $order->id }})" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition-colors border border-slate-700">
                            Resi
                        </button>

                        <a href="{{ route('customer.tracking', $order->id) }}" class="px-3.5 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-cyan-400 font-bold text-xs rounded-xl border border-blue-500/30">
                            Lacak Status
                        </a>

                        @if(in_array($order->status, ['MENUNGGU_PICKUP', 'ORDER_SELESAI', 'COMPLETED', 'CANCELLED']))
                            <button type="button" wire:click="deleteOrder({{ $order->id }})" wire:confirm="Apakah Anda yakin ingin menghapus pesanan {{ $order->order_number }}?" class="px-2.5 py-2 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 font-bold text-xs rounded-xl transition-colors">
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-slate-900/90 rounded-3xl p-12 text-center text-slate-400 border border-slate-800 text-sm">
                Belum ada pesanan laundry aktif atau dalam 24 jam terakhir.
            </div>
        @endforelse
    </div>

    <div class="p-4">
        {{ $orders->links() }}
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
