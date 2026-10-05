<div class="space-y-8">
    <!-- Header & Period Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Laporan & Analitik Outlet</h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Ringkasan pendapatan, item terlaris, dan kinerja driver.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center space-x-1.5 bg-slate-900/90 p-1.5 rounded-2xl border border-slate-800/80 shadow-xl backdrop-blur-xl">
                <button wire:click="$set('period', 'today')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'today' ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">Hari Ini</button>
                <button wire:click="$set('period', 'week')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'week' ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">Minggu Ini</button>
                <button wire:click="$set('period', 'month')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'month' ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">Bulan Ini</button>
                <button wire:click="$set('period', 'all')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'all' ? 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-md shadow-blue-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">Semua Waktu</button>
            </div>

            <a href="{{ route('admin.reports.pdf', ['period' => $period]) }}" target="_blank" class="px-4 py-2 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-xs rounded-2xl shadow-lg transition-all flex items-center space-x-1.5 cursor-pointer hover:scale-105 active:scale-95">
                <span>Export PDF Laporan</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl">
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
            <p class="text-2xl font-black text-emerald-400 mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl">
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Order Masuk</span>
            <p class="text-2xl font-black text-white mt-2">{{ $totalOrders }}</p>
        </div>
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl">
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Order Selesai</span>
            <p class="text-2xl font-black text-cyan-400 mt-2">{{ $completedOrders }}</p>
        </div>
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl">
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Order Dibatalkan</span>
            <p class="text-2xl font-black text-rose-400 mt-2">{{ $cancelledOrders }}</p>
        </div>
    </div>

    <!-- Breakdown Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Top Selling Items Left -->
        <div class="lg:col-span-6 bg-slate-900/90 rounded-3xl p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl space-y-4">
            <h3 class="font-extrabold text-white text-base">5 Item Laundry Terlaris</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Nama Item</th>
                            <th class="py-3 px-3">Jumlah (Piece)</th>
                            <th class="py-3 px-3 text-right">Omset Item</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($topItems as $item)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3 font-bold text-slate-200 text-xs">{{ $item->service_name }}</td>
                                <td class="py-3 px-3 font-extrabold text-cyan-400 text-xs">{{ $item->total_qty }} pcs</td>
                                <td class="py-3 px-3 text-right font-mono font-extrabold text-white text-xs">
                                    Rp{{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-500 text-xs">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Driver Performance Right -->
        <div class="lg:col-span-6 bg-slate-900/90 rounded-3xl p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl space-y-4">
            <h3 class="font-extrabold text-white text-base">Kinerja Penugasan Driver</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Nama Driver</th>
                            <th class="py-3 px-3">Pickup Selesai</th>
                            <th class="py-3 px-3 text-right">Delivery Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($drivers as $driver)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-3 font-bold text-slate-200 text-xs">{{ $driver->name }}</td>
                                <td class="py-3 px-3 font-bold text-cyan-400 text-xs">{{ $driver->completed_pickups }} Pickups</td>
                                <td class="py-3 px-3 text-right font-bold text-indigo-400 text-xs">{{ $driver->completed_deliveries }} Deliveries</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-500 text-xs">Belum ada data driver.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
