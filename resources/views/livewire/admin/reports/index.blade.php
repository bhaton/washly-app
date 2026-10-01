<div class="space-y-8">
    <!-- Header & Period Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laporan & Analitik Outlet</h1>
            <p class="text-sm text-slate-500">Ringkasan pendapatan, item terlaris, dan kinerja driver.</p>
        </div>
        <div class="flex items-center space-x-2 bg-white p-1 rounded-2xl border border-slate-200 shadow-xs">
            <button wire:click="$set('period', 'today')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'today' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">Hari Ini</button>
            <button wire:click="$set('period', 'week')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'week' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">Minggu Ini</button>
            <button wire:click="$set('period', 'month')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'month' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">Bulan Ini</button>
            <button wire:click="$set('period', 'all')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">Semua Waktu</button>
        </div>
    </div>

    <!-- Summary KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
            <p class="text-2xl font-black text-emerald-600 mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Order Masuk</span>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Order Selesai</span>
            <p class="text-2xl font-black text-indigo-600 mt-2">{{ $completedOrders }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Order Dibatalkan</span>
            <p class="text-2xl font-black text-rose-500 mt-2">{{ $cancelledOrders }}</p>
        </div>
    </div>

    <!-- Breakdown Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Top Selling Items Left -->
        <div class="lg:col-span-6 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base">5 Item Laundry Terlaris</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Nama Item</th>
                            <th class="py-3 px-3">Jumlah (Piece)</th>
                            <th class="py-3 px-3 text-right">Omset Item</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topItems as $item)
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-800 text-xs">{{ $item->service_name }}</td>
                                <td class="py-3 px-3 font-extrabold text-indigo-600 text-xs">{{ $item->total_qty }} pcs</td>
                                <td class="py-3 px-3 text-right font-mono font-extrabold text-slate-900 text-xs">
                                    Rp{{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400 text-xs">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Driver Performance Right -->
        <div class="lg:col-span-6 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base">Kinerja Penugasan Driver</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Nama Driver</th>
                            <th class="py-3 px-3">Pickup Selesai</th>
                            <th class="py-3 px-3 text-right">Delivery Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($drivers as $driver)
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-800 text-xs">{{ $driver->name }}</td>
                                <td class="py-3 px-3 font-bold text-indigo-600 text-xs">{{ $driver->completed_pickups }} Pickups</td>
                                <td class="py-3 px-3 text-right font-bold text-violet-600 text-xs">{{ $driver->completed_deliveries }} Deliveries</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400 text-xs">Belum ada data driver.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
