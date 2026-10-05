<div class="space-y-6">
    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Manajemen Pesanan Laundry</h1>
            <p class="text-sm text-slate-400">Konfirmasi order, tugaskan driver pickup/delivery, dan update status proses.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 rounded-2xl text-sm font-semibold flex items-center justify-between">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search & Status/Date Filter Bar -->
    <div class="bg-slate-900/90 rounded-3xl p-4 sm:p-6 border border-slate-800 shadow-xl flex flex-col md:flex-row gap-4 justify-between">
        <div class="w-full md:w-5/12">
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Cari Order / Customer</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nomor order (ORD-...) atau nama customer..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="w-full md:w-4/12">
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Filter Status Order</label>
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 font-medium">
                <option value="">Semua Status Order</option>
                <option value="MENUNGGU_PICKUP">Menunggu Pickup</option>
                <option value="DRIVER_DITUGASKAN">Driver Pickup Ditugaskan</option>
                <option value="LAUNDRY_DIJEMPUT">Laundry Dijemput</option>
                <option value="LAUNDRY_DITERIMA">Laundry Diterima Outlet</option>
                <option value="PROSES_LAUNDRY">Proses Laundry</option>
                <option value="LAUNDRY_SELESAI">Laundry Selesai</option>
                <option value="TAGIHAN_DIBUAT">Tagihan Dibuat</option>
                <option value="DRIVER_PENGIRIMAN_DITUGASKAN">Driver Delivery Ditugaskan</option>
                <option value="LAUNDRY_DIKEMBALIKAN">Laundry Dikembalikan</option>
                <option value="ORDER_SELESAI">Order Selesai</option>
                <option value="CANCELLED">Dibatalkan</option>
            </select>
        </div>
        <div class="w-full md:w-3/12">
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Filter Periode Waktu</label>
            <select wire:model.live="dateFilter" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 font-medium">
                <option value="">Semua Waktu</option>
                <option value="today">Hari Ini</option>
                <option value="this_week">Minggu Ini</option>
                <option value="this_month">Bulan Ini</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-4 px-4">No. Order</th>
                        <th class="py-4 px-4">Customer & WA</th>
                        <th class="py-4 px-4">Item & Total</th>
                        <th class="py-4 px-4">Status & History</th>
                        <th class="py-4 px-4">Driver Assigned</th>
                        <th class="py-4 px-4 text-right">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($orders as $order)
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $order->pickup_phone ?? $order->customer->phone ?? '');
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            $waText = urlencode("Halo {$order->customer->name}, mengenai pesanan laundry {$order->order_number} (Status: " . str_replace('_', ' ', $order->status) . ").");
                            $waUrl = "https://wa.me/{$cleanPhone}?text={$waText}";
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4 font-mono font-bold text-cyan-400 text-xs">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                <span class="block text-[10px] text-slate-500 font-normal font-sans">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-white text-xs">{{ $order->customer->name ?? '-' }}</p>
                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="text-xs text-slate-400 font-mono">{{ $order->pickup_phone }}</span>
                                    <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center space-x-1 px-2 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] rounded-lg transition-all">
                                        <span>WA</span>
                                    </a>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">{{ $order->pickup_address }}</p>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-extrabold text-white text-xs">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                                <span class="text-xs text-slate-400 font-medium block">{{ $order->orderItems->sum('quantity') ?: 1 }} item ({{ strtoupper($order->service_type ?? 'kiloan') }})</span>
                                @php
                                    $isPaid = $order->payment?->status === 'PAID' || in_array($order->payment?->status, ['SUCCESS', 'SETTLEMENT']);
                                @endphp
                                @if($isPaid)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 mt-1">
                                        LUNAS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30 mt-1">
                                        BELUM BAYAR
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                    @if(in_array($order->status, ['ORDER_SELESAI', 'COMPLETED'])) bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                                    @elseif(in_array($order->status, ['MENUNGGU_PICKUP', 'DRIVER_DITUGASKAN'])) bg-amber-500/10 text-amber-400 border border-amber-500/30
                                    @elseif(in_array($order->status, ['PROSES_LAUNDRY', 'LAUNDRY_DITERIMA', 'LAUNDRY_SELESAI', 'PENIMBANGAN'])) bg-blue-500/10 text-cyan-400 border border-blue-500/30
                                    @else bg-purple-500/10 text-purple-400 border border-purple-500/30 @endif">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs space-y-1">
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Pickup:</span>
                                    @if($order->pickupDriver)
                                        <span class="font-semibold text-slate-200">{{ $order->pickupDriver->name }}</span>
                                    @else
                                        <span class="text-amber-400 font-semibold italic">Belum assign</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Delivery:</span>
                                    @if($order->deliveryDriver)
                                        <span class="font-semibold text-slate-200">{{ $order->deliveryDriver->name }}</span>
                                    @else
                                        <span class="text-slate-500 italic">Belum assign</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right space-y-1.5">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 bg-blue-500/10 hover:bg-blue-500/20 text-cyan-400 font-bold text-xs rounded-lg border border-blue-500/30 transition-colors">
                                        Detail & Workflow
                                    </a>
                                    <button type="button" wire:click="confirmDelete({{ $order->id }})" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs rounded-lg border border-rose-500/30 transition-colors">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500 text-sm">Tidak ada data order ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-950/80 border-t border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Assign Driver Modal -->
    @if($showAssignModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-800 text-slate-100">
                <h3 class="font-extrabold text-white text-lg">
                    Assign Driver {{ ucfirst($assignType) }}
                </h3>
                <p class="text-xs text-slate-400">Pilih driver aktif yang akan menerima penugasan {{ $assignType }}.</p>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pilih Driver Lapangan</label>
                    <select wire:model="selectedDriverId" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Pilih Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                        @endforeach
                    </select>
                    @error('selectedDriverId') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-800">
                    <button wire:click="$set('showAssignModal', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                        Batal
                    </button>
                    <button wire:click="assignDriver" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg">
                        Tugaskan Driver
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
                        <p class="text-xs text-slate-400">Order No: <strong class="font-mono text-cyan-400">{{ $orderNumberToDelete }}</strong></p>
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
</div>
