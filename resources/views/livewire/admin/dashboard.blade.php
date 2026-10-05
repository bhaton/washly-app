<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Dashboard Operasional Outlet</h1>
            <p class="text-sm text-slate-400">Ringkasan status order, driver aktif, dan performa harian.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 font-bold text-xs rounded-xl shadow-md transition-all flex items-center space-x-1.5">
                <span>Kelola Admin</span>
            </a>
            <a href="{{ route('admin.drivers.index') }}" class="px-4 py-2 bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 font-bold text-xs rounded-xl shadow-md transition-all flex items-center space-x-1.5">
                <span>Kelola Driver</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                Kelola Semua Order &rarr;
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Revenue -->
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-xs uppercase tracking-wider">RP</div>
            </div>
            <p class="text-2xl font-extrabold text-white mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <span class="text-xs text-emerald-400 font-semibold mt-1 inline-block">● Terkonfirmasi QRIS</span>
        </div>

        <!-- Today Orders -->
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Order Masuk Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-cyan-400 border border-blue-500/30 flex items-center justify-center font-bold text-xs uppercase tracking-wider">ORD</div>
            </div>
            <p class="text-2xl font-extrabold text-white mt-2">{{ $todayOrdersCount }}</p>
            <span class="text-xs text-slate-400 mt-1 inline-block">Pesanan baru</span>
        </div>

        <!-- Waiting Pickup -->
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Pickup</span>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center justify-center font-bold text-xs uppercase tracking-wider">PICK</div>
            </div>
            <p class="text-2xl font-extrabold text-amber-400 mt-2">{{ $waitingPickupCount }}</p>
            <span class="text-xs text-amber-400 font-semibold mt-1 inline-block">Perlu assign driver</span>
        </div>

        <!-- Processing -->
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-cyan-400 border border-blue-500/30 flex items-center justify-center font-bold text-xs uppercase tracking-wider">PROC</div>
            </div>
            <p class="text-2xl font-extrabold text-cyan-400 mt-2">{{ $processingCount }}</p>
            <span class="text-xs text-cyan-400 font-semibold mt-1 inline-block">Di tempat cuci</span>
        </div>
    </div>

    <!-- Additional Secondary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-800 text-center">
            <span class="text-xs text-slate-400 font-semibold">Siap Diantar</span>
            <p class="text-xl font-bold text-white mt-1">{{ $readyDeliveryCount }}</p>
        </div>
        <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-800 text-center">
            <span class="text-xs text-slate-400 font-semibold">Delivery Aktif</span>
            <p class="text-xl font-bold text-white mt-1">{{ $activeDeliveryCount }}</p>
        </div>
        <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-800 text-center">
            <span class="text-xs text-slate-400 font-semibold">Order Selesai</span>
            <p class="text-xl font-bold text-emerald-400 mt-1">{{ $completedCount }}</p>
        </div>
        <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-800 text-center">
            <span class="text-xs text-slate-400 font-semibold">Driver Aktif</span>
            <p class="text-xl font-bold text-white mt-1">{{ $activeDrivers->count() }}</p>
        </div>
    </div>

    <!-- Recent Orders & Driver Operational Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Orders Table Left -->
        <div class="lg:col-span-8 bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-white text-lg">10 Order Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-cyan-400 hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/80 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">No. Order</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Total</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-3 font-mono font-bold text-cyan-400 text-xs">
                                    {{ $order->order_number }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <p class="font-bold text-white text-xs">{{ $order->customer?->name ?? 'Pelanggan Umum' }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->customer?->phone ?? '-' }}</p>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                        @if($order->status === 'COMPLETED' || $order->status === 'ORDER_SELESAI') bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                                        @elseif(in_array($order->status, ['PENDING_PAYMENT', 'WAITING_CONFIRMATION', 'MENUNGGU_PICKUP'])) bg-amber-500/10 text-amber-400 border border-amber-500/30
                                        @else bg-blue-500/10 text-cyan-400 border border-blue-500/30 @endif">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-extrabold text-white text-xs">
                                    Rp{{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-lg transition-colors border border-slate-700">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 text-sm">Belum ada order masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Driver Active Tasks Right -->
        <div class="lg:col-span-4 bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-6">
            <h3 class="font-extrabold text-white text-lg">Beban Kerja Driver Lapangan</h3>

            <div class="space-y-4">
                @forelse($activeDrivers as $driver)
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shadow-md shadow-blue-500/20">
                                    {{ strtoupper(substr($driver->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-white text-xs">{{ $driver->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $driver->phone }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Aktif</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-center text-xs pt-2 border-t border-slate-800">
                            <div class="bg-slate-900 p-2 rounded-xl border border-slate-800">
                                <span class="text-slate-400 block text-[10px]">Pickup Active</span>
                                <span class="font-bold text-cyan-400 text-sm">{{ $driver->active_pickups_count }}</span>
                            </div>
                            <div class="bg-slate-900 p-2 rounded-xl border border-slate-800">
                                <span class="text-slate-400 block text-[10px]">Delivery Active</span>
                                <span class="font-bold text-sky-400 text-sm">{{ $driver->active_deliveries_count }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">Belum ada driver aktif.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
