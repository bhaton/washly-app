<div class="space-y-6">
    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pesanan Laundry</h1>
            <p class="text-sm text-slate-500">Konfirmasi order, tugaskan driver pickup/delivery, dan update status proses.</p>
        </div>
    </div>

    <!-- Search & Status Filter Bar -->
    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row gap-4 justify-between">
        <div class="w-full md:w-1/2">
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Cari Order / Customer</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nomor order (ORD-...) atau nama customer..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="w-full md:w-1/3">
            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Filter Status Order</label>
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium">
                <option value="">Semua Status Order</option>
                <option value="PENDING_PAYMENT">PENDING_PAYMENT (Menunggu Bayar)</option>
                <option value="PAID">PAID (Sudah Bayar Demo)</option>
                <option value="WAITING_CONFIRMATION">WAITING_CONFIRMATION</option>
                <option value="CONFIRMED">CONFIRMED (Dikonfirmasi Outlet)</option>
                <option value="WAITING_PICKUP">WAITING_PICKUP</option>
                <option value="PICKUP_ASSIGNED">PICKUP_ASSIGNED (Driver Ditugaskan)</option>
                <option value="DRIVER_GOING_TO_PICKUP">DRIVER_GOING_TO_PICKUP</option>
                <option value="PICKED_UP">PICKED_UP (Laundry Dijemput)</option>
                <option value="RECEIVED_AT_OUTLET">RECEIVED_AT_OUTLET (Diterima Outlet)</option>
                <option value="PROCESSING">PROCESSING (Sedang Diproses)</option>
                <option value="READY_FOR_DELIVERY">READY_FOR_DELIVERY (Siap Diantar)</option>
                <option value="DELIVERY_ASSIGNED">DELIVERY_ASSIGNED (Driver Ditugaskan)</option>
                <option value="DRIVER_GOING_TO_CUSTOMER">DRIVER_GOING_TO_CUSTOMER</option>
                <option value="DELIVERED">DELIVERED (Sampai Customer)</option>
                <option value="COMPLETED">COMPLETED (Selesai)</option>
                <option value="CANCELLED">CANCELLED (Dibatalkan)</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-4 px-4">No. Order</th>
                        <th class="py-4 px-4">Customer</th>
                        <th class="py-4 px-4">Item & Total</th>
                        <th class="py-4 px-4">Status & History</th>
                        <th class="py-4 px-4">Driver Assigned</th>
                        <th class="py-4 px-4 text-right">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-4 font-mono font-bold text-slate-900 text-xs">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                <span class="block text-[10px] text-slate-400 font-normal font-sans">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-slate-800 text-xs">{{ $order->customer->name }}</p>
                                <p class="text-xs text-slate-500">{{ $order->pickup_phone }}</p>
                                <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ $order->pickup_address }}</p>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-extrabold text-slate-900 text-xs">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                                <span class="text-xs text-slate-500">{{ $order->orderItems->sum('quantity') }} items</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($order->status === 'COMPLETED') bg-emerald-100 text-emerald-800
                                    @elseif(in_array($order->status, ['PENDING_PAYMENT', 'WAITING_CONFIRMATION', 'WAITING_PICKUP'])) bg-amber-100 text-amber-800
                                    @elseif(in_array($order->status, ['PROCESSING', 'RECEIVED_AT_OUTLET', 'READY_FOR_DELIVERY'])) bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs space-y-1">
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Pickup:</span>
                                    @if($order->pickupDriver)
                                        <span class="font-semibold text-slate-800">{{ $order->pickupDriver->name }}</span>
                                    @else
                                        <span class="text-amber-500 font-semibold italic">Belum assign</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Delivery:</span>
                                    @if($order->deliveryDriver)
                                        <span class="font-semibold text-slate-800">{{ $order->deliveryDriver->name }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum assign</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right space-y-1">
                                <!-- Contextual State Transition Buttons -->
                                @if(in_array($order->status, ['PAID', 'WAITING_CONFIRMATION']))
                                    <button wire:click="confirmOrder({{ $order->id }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all w-full">
                                        ✓ Konfirmasi Order
                                    </button>
                                @elseif(in_array($order->status, ['CONFIRMED', 'WAITING_PICKUP', 'PICKUP_ASSIGNED']))
                                    <button wire:click="openAssignModal({{ $order->id }}, 'pickup')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all w-full">
                                        🛵 Assign Driver Pickup
                                    </button>
                                @elseif($order->status === 'PICKED_UP')
                                    <button wire:click="updateStatus({{ $order->id }}, 'RECEIVED_AT_OUTLET')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all w-full">
                                        🧺 Terima di Outlet
                                    </button>
                                @elseif($order->status === 'RECEIVED_AT_OUTLET')
                                    <button wire:click="updateStatus({{ $order->id }}, 'PROCESSING')" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all w-full">
                                        ⚙️ Mulai Proses Laundry
                                    </button>
                                @elseif($order->status === 'PROCESSING')
                                    <button wire:click="updateStatus({{ $order->id }}, 'READY_FOR_DELIVERY')" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all w-full">
                                        ✨ Siap Diantar
                                    </button>
                                @elseif(in_array($order->status, ['READY_FOR_DELIVERY', 'DELIVERY_ASSIGNED']))
                                    <button wire:click="openAssignModal({{ $order->id }}, 'delivery')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all w-full">
                                        🚚 Assign Driver Delivery
                                    </button>
                                @endif

                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-block text-center px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors w-full">
                                    Lihat Detail & Timeline
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">Tidak ada data order ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Assign Driver Modal -->
    @if($showAssignModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="font-extrabold text-slate-900 text-lg">
                    Assign Driver {{ ucfirst($assignType) }}
                </h3>
                <p class="text-xs text-slate-500">Pilih driver aktif yang akan menerima penugasan {{ $assignType }}.</p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Driver Lapangan</label>
                    <select wire:model="selectedDriverId" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                        @endforeach
                    </select>
                    @error('selectedDriverId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showAssignModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                        Batal
                    </button>
                    <button wire:click="assignDriver" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md">
                        Tugaskan Driver
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
