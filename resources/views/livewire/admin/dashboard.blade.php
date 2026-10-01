<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard Operasional Outlet</h1>
            <p class="text-sm text-slate-500">Ringkasan status order, driver aktif, dan performa harian.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                Kelola Semua Order &rarr;
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Revenue -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">💰</div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <span class="text-xs text-emerald-600 font-semibold mt-1 inline-block">● Terkonfirmasi QRIS</span>
        </div>

        <!-- Today Orders -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Order Masuk Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">📦</div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">{{ $todayOrdersCount }}</p>
            <span class="text-xs text-slate-500 mt-1 inline-block">Pesanan baru</span>
        </div>

        <!-- Waiting Pickup -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Pickup</span>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">🛵</div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 mt-2">{{ $waitingPickupCount }}</p>
            <span class="text-xs text-amber-600 font-semibold mt-1 inline-block">Perlu assign driver</span>
        </div>

        <!-- Processing -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</span>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">🧺</div>
            </div>
            <p class="text-2xl font-extrabold text-blue-600 mt-2">{{ $processingCount }}</p>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Di tempat cuci</span>
        </div>
    </div>

    <!-- Additional Secondary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200 text-center">
            <span class="text-xs text-slate-500 font-semibold">Siap Diantar</span>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ $readyDeliveryCount }}</p>
        </div>
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200 text-center">
            <span class="text-xs text-slate-500 font-semibold">Delivery Aktif</span>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ $activeDeliveryCount }}</p>
        </div>
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200 text-center">
            <span class="text-xs text-slate-500 font-semibold">Order Selesai</span>
            <p class="text-xl font-bold text-emerald-600 mt-1">{{ $completedCount }}</p>
        </div>
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200 text-center">
            <span class="text-xs text-slate-500 font-semibold">Driver Aktif</span>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ $activeDrivers->count() }}</p>
        </div>
    </div>

    <!-- Recent Orders & Driver Operational Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Orders Table Left -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 text-lg">10 Order Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">No. Order</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Total</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3.5 px-3 font-mono font-bold text-slate-900 text-xs">
                                    {{ $order->order_number }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <p class="font-bold text-slate-800 text-xs">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->customer->phone }}</p>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                        @if($order->status === 'COMPLETED') bg-emerald-100 text-emerald-800
                                        @elseif(in_array($order->status, ['PENDING_PAYMENT', 'WAITING_CONFIRMATION'])) bg-amber-100 text-amber-800
                                        @elseif(in_array($order->status, ['PROCESSING', 'RECEIVED_AT_OUTLET'])) bg-blue-100 text-blue-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-extrabold text-slate-900 text-xs">
                                    Rp{{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada order masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Driver Active Tasks Right -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6">
            <h3 class="font-extrabold text-slate-900 text-lg">Beban Kerja Driver Lapangan</h3>

            <div class="space-y-4">
                @forelse($activeDrivers as $driver)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($driver->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $driver->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $driver->phone }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Aktif</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-center text-xs pt-2 border-t border-slate-200">
                            <div class="bg-white p-2 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block">Pickup Active</span>
                                <span class="font-bold text-indigo-600 text-sm">{{ $driver->active_pickups_count }}</span>
                            </div>
                            <div class="bg-white p-2 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block">Delivery Active</span>
                                <span class="font-bold text-violet-600 text-sm">{{ $driver->active_deliveries_count }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada driver aktif.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
