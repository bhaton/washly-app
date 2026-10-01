<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.index') }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600">
                    &larr;
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Detail Order {{ $order->order_number }}</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
        </div>

        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold shadow-xs
            @if($order->status === 'COMPLETED') bg-emerald-100 text-emerald-800 border border-emerald-200
            @elseif(in_array($order->status, ['PENDING_PAYMENT', 'WAITING_CONFIRMATION'])) bg-amber-100 text-amber-800 border border-amber-200
            @elseif(in_array($order->status, ['PROCESSING', 'RECEIVED_AT_OUTLET'])) bg-blue-100 text-blue-800 border border-blue-200
            @else bg-cyan-100 text-cyan-800 border border-cyan-200 @endif">
            STATUS: {{ str_replace('_', ' ', $order->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Details Left -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Item List Snapshot Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Rincian Item Laundry (Snapshot Price)</h3>

                <div class="divide-y divide-slate-100">
                    @foreach($order->orderItems as $item)
                        <div class="py-3 flex justify-between items-center text-sm">
                            <div>
                                <p class="font-bold text-slate-800">{{ $item->service_name }}</p>
                                <span class="text-xs text-slate-400">Rp{{ number_format($item->unit_price, 0, ',', '.') }} x {{ $item->quantity }} pcs</span>
                            </div>
                            <span class="font-extrabold text-slate-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-200 space-y-1.5 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-600 font-bold">
                        <span>Ongkos Kirim (Antar Jemput)</span>
                        <span>GRATIS (Rp 0)</span>
                    </div>
                    <div class="flex justify-between text-lg font-extrabold text-slate-900 pt-2 border-t border-slate-100">
                        <span>Total Pembayaran</span>
                        <span class="text-blue-600">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Proof Photos Section if exists -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Pickup Proof -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-2">
                    <h4 class="font-bold text-slate-800 text-sm">Bukti Foto Pickup</h4>
                    @if($order->pickupProof)
                        <img src="{{ Storage::url($order->pickupProof->image_path) }}" class="w-full h-48 object-cover rounded-2xl border border-slate-200">
                        <p class="text-xs text-slate-500">Diunggah oleh: {{ $order->pickupProof->driver->name }}</p>
                        @if($order->pickupProof->notes) <p class="text-xs italic text-slate-600">"{{ $order->pickupProof->notes }}"</p> @endif
                    @else
                        <div class="h-32 bg-slate-50 rounded-2xl border border-dashed border-slate-200 flex items-center justify-center text-xs text-slate-400">
                            Belum ada bukti pickup
                        </div>
                    @endif
                </div>

                <!-- Delivery Proof -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-2">
                    <h4 class="font-bold text-slate-800 text-sm">Bukti Foto Delivery</h4>
                    @if($order->deliveryProof)
                        <img src="{{ Storage::url($order->deliveryProof->image_path) }}" class="w-full h-48 object-cover rounded-2xl border border-slate-200">
                        <p class="text-xs text-slate-500">Diunggah oleh: {{ $order->deliveryProof->driver->name }}</p>
                        @if($order->deliveryProof->notes) <p class="text-xs italic text-slate-600">"{{ $order->deliveryProof->notes }}"</p> @endif
                    @else
                        <div class="h-32 bg-slate-50 rounded-2xl border border-dashed border-slate-200 flex items-center justify-center text-xs text-slate-400">
                            Belum ada bukti delivery
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status History Timeline -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Riwayat Perubahan Status Order</h3>
                <div class="relative pl-6 border-l-2 border-slate-200 space-y-6">
                    @foreach($order->statusHistories as $history)
                        <div class="relative">
                            <div class="absolute -left-[31px] top-1 w-4 h-4 rounded-full bg-blue-600 border-2 border-white"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ str_replace('_', ' ', $history->to_status) }}</p>
                                    <p class="text-xs text-slate-500">
                                        Oleh: <span class="font-semibold">{{ $history->changed_by_role ?? 'System' }}</span>
                                        @if($history->changedBy) ({{ $history->changedBy->name }}) @endif
                                    </p>
                                    @if($history->notes) <p class="text-xs text-slate-600 mt-0.5 font-medium">"{{ $history->notes }}"</p> @endif
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $history->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Right: Customer & Driver Assignments -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Operational Action Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
                <h3 class="font-extrabold text-slate-900 text-sm">Aksi Operasional Status</h3>
                <div class="space-y-2">
                    @if(in_array($order->status, ['PAID', 'WAITING_CONFIRMATION']))
                        <button wire:click="transitionTo('CONFIRMED', app('App\\Services\\OrderStatusService'))" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            ✓ Konfirmasi Order Outlet
                        </button>
                    @elseif($order->status === 'CONFIRMED')
                        <button wire:click="transitionTo('WAITING_PICKUP', app('App\\Services\\OrderStatusService'))" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            🛵 Siapkan Penugasan Pickup
                        </button>
                    @elseif($order->status === 'PICKED_UP')
                        <button wire:click="transitionTo('RECEIVED_AT_OUTLET', app('App\\Services\\OrderStatusService'))" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            🧺 Terima Laundry di Outlet
                        </button>
                    @elseif($order->status === 'RECEIVED_AT_OUTLET')
                        <button wire:click="transitionTo('PROCESSING', app('App\\Services\\OrderStatusService'))" class="w-full py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            ⚙️ Mulai Proses Laundry
                        </button>
                    @elseif($order->status === 'PROCESSING')
                        <button wire:click="transitionTo('READY_FOR_DELIVERY', app('App\\Services\\OrderStatusService'))" class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            ✨ Tandai Siap Diantar
                        </button>
                    @elseif($order->status === 'COMPLETED')
                        <div class="p-2.5 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-xl text-center">
                            ✓ Pesanan Selesai (Completed)
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
                <h3 class="font-extrabold text-slate-900 text-base">Informasi Pelanggan</h3>
                <div class="text-xs space-y-2 text-slate-700">
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Nama</span>
                        <p class="font-bold text-sm text-slate-900">{{ $order->customer->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Nomor HP / WhatsApp</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->pickup_phone) }}" target="_blank" class="font-bold text-emerald-600 hover:underline">
                            {{ $order->pickup_phone }} 📱
                        </a>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Alamat Pickup</span>
                        <p class="font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-200 mt-1">{{ $order->pickup_address }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold uppercase">Alamat Delivery</span>
                        <p class="font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-200 mt-1">{{ $order->delivery_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Driver Pickup Assignment Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
                <h3 class="font-extrabold text-slate-900 text-sm">Penugasan Driver Pickup</h3>
                <div class="space-y-3">
                    <select wire:model="selectedPickupDriverId" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                        <option value="">-- Belum Ada Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                        @endforeach
                    </select>
                    <button wire:click="assignPickupDriver" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs">
                        Assign Pickup Driver
                    </button>
                </div>
            </div>

            <!-- Driver Delivery Assignment Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
                <h3 class="font-extrabold text-slate-900 text-sm">Penugasan Driver Delivery</h3>
                <div class="space-y-3">
                    <select wire:model="selectedDeliveryDriverId" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
                        <option value="">-- Belum Ada Driver --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                        @endforeach
                    </select>
                    <button wire:click="assignDeliveryDriver" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs">
                        Assign Delivery Driver
                    </button>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2">
                <h3 class="font-extrabold text-slate-900 text-sm">Status Pembayaran Demo</h3>
                @if($order->payment)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs space-y-1">
                        <div class="flex justify-between font-bold text-emerald-800">
                            <span>METODE</span>
                            <span>{{ $order->payment->payment_method }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-mono">
                            <span>REF</span>
                            <span>{{ $order->payment->payment_reference }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-emerald-700">
                            <span>STATUS</span>
                            <span>{{ $order->payment->status }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-amber-600 font-semibold">Belum ada record pembayaran.</p>
                @endif
            </div>
        </div>
    </div>
</div>
